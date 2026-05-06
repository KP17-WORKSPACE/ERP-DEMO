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
use App\SystemNotification;
use Illuminate\Support\Facades\Hash;

use function GuzzleHttp\Promise\exception_for;


class SysSalesInvoiceNewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $siv_id = null)
    {
        //return session_id();
        //  try {
        //      return $this->generateDeal(3257);
        //  } catch (\Throwable $th) {
        //      return $th;
        //  }


        try {
            $filter_by = "";
            $ctrl_date = "";
            $ctrl_date2 = "";
            $pending_dn = 0;
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $customer_list = SysHelper::get_customer_list($company_id);
            $sales_person_list = SysHelper::get_sales_persons();

            $ctrl_date = "";
            $ctrl_date2 = "";
            $ctrl_doc_number = "";
            $ctrl_customer = "";
            $ctrl_supplier = "";
            $ctrl_deal_number = "";
            $ctrl_delivery_note = "";
            $ctrl_srt = "";
            $ctrl_amount = "";
            $ctrl_sales_person = "";
            $ctrl_attachments = "";
            $filter_by = "";


            $query = SysSalesInvoice::select(DB::raw('sys_sales_invoice.*, (SELECT GROUP_CONCAT(doc_file) FROM sys_sales_invoice_att WHERE siv_id = sys_sales_invoice.id) AS attach, (SELECT GROUP_CONCAT(doc_number) FROM sys_delivery_note WHERE invoice_no = sys_sales_invoice.doc_number) AS dlnno, (SELECT GROUP_CONCAT(doc_number) FROM sys_sales_return WHERE si_doc_number = sys_sales_invoice.doc_number) AS srtno, (SELECT max(debit_amount) FROM sys_chartofaccounts_transaction WHERE transaction_type="salesinvoice" and transaction_no=sys_sales_invoice.doc_number and account_id=sys_sales_invoice.customer) AS amount, (SELECT GROUP_CONCAT(code) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS code'), DB::raw('(SELECT SUM(vatamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_vatamount'), DB::raw('(SELECT SUM(taxableamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_taxableamount'));


            $query->wherein('company_id', $company_id);

            if (SysHelper::get_pagination_post($request)) {
                if ($request->from_date != "" && $request->filter_by == "") {
                    $ctrl_date = SysHelper::normalizeToYmd($request->from_date);
                }
                if ($request->to_date != "" && $request->filter_by == "") {
                    $ctrl_date2 = SysHelper::normalizeToYmd($request->to_date);
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
                    $ctrl_doc_number = $request->documents_number;
                }
                if ($request->customer != "") {
                    $query->where('customer', $request->customer);
                    $ctrl_customer = $request->customer;
                }
                if ($request->supplier != "") {
                    $query->where('supplier_name', 'like', '%' . $request->supplier . '%');
                    $ctrl_supplier = $request->supplier;
                }
                if ($request->deal_number != "") {
                    $query->where('deal_id', 'like', '%' . SysHelper::get_dealid_from_code($request->deal_number) . '%');
                    $ctrl_deal_number = $request->deal_number;
                }
                if ($request->delivery_note != "") {
                    if (strtolower($request->delivery_note) == "pending") {
                        $pending_dn = 1;
                    } else {
                        $del_nos = SysDeliveryNote::where('doc_number', $request->delivery_note)->pluck('id');
                        $query->wherein('dn_id', $del_nos);
                    }
                    $ctrl_delivery_note = $request->delivery_note;
                }
                if ($request->srt != "") {
                    $srt_nos = SysSalesReturn::where('doc_number', $request->srt)->pluck('si_doc_number');
                    $query->wherein('doc_number', $srt_nos);
                    $ctrl_srt = $request->srt;
                }
                if ($request->amount != "") {
                    //$amt_nos = SysChartofAccountsTransaction::where('transaction_type', 'salesinvoice')->whereBetween('debit_amount',[$request->amount, $request->amount])->pluck('transaction_no');
                    $amt_nos = SysChartofAccountsTransaction::select('transaction_no', DB::raw('SUM(debit_amount) as total_debit_amount'))
                        ->where('transaction_type', 'salesinvoice')
                        ->groupBy('transaction_no')
                        ->havingRaw('SUM(debit_amount) BETWEEN ? AND ?', [$request->amount, $request->amount])  // Filter by the sum
                        ->pluck('transaction_no');
                    $query->wherein('doc_number', $amt_nos);
                    $ctrl_amount = $request->amount;
                }
                if ($ctrl_date != "" && $ctrl_date2 != "") {
                    $query->whereBetween('doc_date', [$ctrl_date, $ctrl_date2]);
                }
                if ($ctrl_date != "" && $ctrl_date2 == "") {
                    $query->where('doc_date', '>=', $ctrl_date);
                }
                if ($ctrl_date == "" && $ctrl_date2 != "") {
                    $query->where('doc_date', '<=', $ctrl_date2);
                }
                if ($request->sales_person != "") {
                    $query->where('sales_man', $request->sales_person);
                    $ctrl_sales_person = $request->sales_person;
                }
                if ($request->attachments == 1) {
                    $att_nos = DB::table('sys_sales_invoice_att')->wherein('company_id', $company_id)->pluck('siv_id');
                    $query->wherein('id', $att_nos);
                    $ctrl_attachments = 1;
                }
                if ($request->attachments == 2) {
                    $att_nos = DB::table('sys_sales_invoice_att')->wherein('company_id', $company_id)->pluck('siv_id');
                    $query->wherenotin('id', $att_nos);
                    $ctrl_attachments = 2;
                }
            } else {

            }

            $query->orderby('doc_number', 'desc');

            if (SysHelper::get_pagination_post($request)) {
                $salesinvoice = $query->get();
            } else {
                $salesinvoice = $query->paginate(30);
            }


            $adj_list = SysReceiptAdjustments::select('bi_doc_number', 'bi_doc_no', 'bi_doc_date', 'bi_lpo_no', 'bi_total', 'bi_paid', 'bi_balance', 'bi_amount', 'bi_balance_to_adjust', 'bi_cheque_amount', 'bi_amount_adjusted')->wherein('company_id', $company_id)->get();



            $active_id = $siv_id;
            $data = [];


            $action = false;
            $editData = [];

            $addData = [];




            if ($request->has('si_action')) {
                $poAction = $request->input('si_action');

                if ($poAction === 'add') {
                    $action = 'add';


                    $addData = $this->create(); // Get all data for adding

                } elseif ($poAction === 'edit') {
                    $action = 'edit';
                    $editData = $this->edit($active_id); // Get all data for editing;
                    $adj_list = SysReceiptAdjustments::select('bi_doc_number', 'bi_doc_no', 'bi_doc_date', 'bi_lpo_no', 'bi_total', 'bi_paid', 'bi_balance', 'bi_amount', 'bi_balance_to_adjust', 'bi_cheque_amount', 'bi_amount_adjusted')->wherein('company_id', $company_id)->where('bi_doc_no', $editData['edit_si']->doc_number)->get();

                }
            } else {
                if ($siv_id) {
                    $data = $this->get_si_pdf_data($siv_id);
                } else {
                    $firstRecord = $salesinvoice->first();
                    if ($firstRecord) {
                        $active_id = $firstRecord->id;
                        $data = $this->get_si_pdf_data($firstRecord->id);
                    }
                }
            }

            return view('backEnd/salesinvoice/sales_invoice_list', compact('salesinvoice', 'customer_list', 'adj_list', 'sales_person_list', 'filter_by', 'pending_dn', 'data', 'active_id', 'action', 'editData', 'addData', 'ctrl_customer', 'ctrl_supplier', 'ctrl_deal_number', 'ctrl_delivery_note', 'ctrl_srt', 'ctrl_amount', 'ctrl_sales_person', 'ctrl_attachments', 'ctrl_date', 'ctrl_date2', 'ctrl_doc_number'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function search(Request $request)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $query = $request->get('query');

            $invoices = SysSalesInvoice::select(
                'sys_sales_invoice.id',
                'sys_sales_invoice.doc_number',
                'sys_sales_invoice.doc_date',
                DB::raw('(SELECT MAX(debit_amount) 
                  FROM sys_chartofaccounts_transaction 
                  WHERE transaction_type="salesinvoice" 
                  AND transaction_no=sys_sales_invoice.doc_number 
                  AND account_id=sys_sales_invoice.customer) AS amount'),
                'sys_chartofaccounts.account_code',
                'sys_chartofaccounts.account_name',
                'sys_currency.code as currency_code'
            )
                ->join('sys_chartofaccounts', 'sys_chartofaccounts.id', '=', 'sys_sales_invoice.customer')
                ->join('sys_currency', 'sys_currency.id', '=', 'sys_sales_invoice.currency')
                ->whereIn('sys_sales_invoice.company_id', $company_id)
                ->where('sys_sales_invoice.doc_number', 'LIKE', "%{$query}%")
                ->orderBy('sys_sales_invoice.doc_number', 'desc')
                ->limit(20)
                ->get();
            return response()->json($invoices);
        } catch (\Throwable $th) {
            return $th;
        }
    }


    public function getDetails($id)
    {
        $data = $this->get_si_pdf_data($id);
        if (count($data) > 0) {
            return view('backEnd.salesinvoice.si_details', $data);
        } else {
            return "error!!";
        }

    }

    public function getDetailsPDF($id)
    {
        $data = $this->get_si_pdf_data($id);
        if (count($data) > 0) {
            return view('backEnd.salesinvoice.si_details-pdf', $data);
        } else {
            return "error!!";
        }

    }

    public function get_si_pdf_data($id)
    {
        try {
            $address = "";
            $address2 = "";
            $city = "";
            $state = "";
            $country = "";
            $contact_name = "";
            $email = "";
            $tel = "";
            $mob = "";
            $ship_company_name = "";
            $delivery_city = "";
            $delivery_zip_code = "";
            $delivery_country = "";
            $delivery_state = "";
            $cust_trn_no = "";
            $shipp_trn_no = "";

            $si = SysSalesInvoice::find($id);
            if (!empty($si)) {
                $company = SysCompany::find($si->company_id);
                $si_item = SysSalesInvoiceItems::where('si_id', '=', $si->id)->orderBy('sort_id')->get();
                $sup_email = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_chartofaccounts.id', $si->customer)->first();
                if (!empty($sup_email)) {
                    $add = SysCustSupplAddressbook::where('cust_suppl_id', $sup_email->id)->first();
                }

                $contact_name = $sup_email->customer_salutation . ' ' . $sup_email->first_name . ' ' . $sup_email->last_name;
                $email = $sup_email->email;
                $tel = $sup_email->contcat_number;
                $mob = $sup_email->mobile;

                $ship_tel = $tel;
                $ship_mob = $mob;

                $cust_trn_no = $sup_email->vat_number;
                //$shipp_trn_no = $sup_email->vat_number;

                if (!empty($add)) {
                    $address = $add->address;
                    $address2 = $add->address2;
                    $city = $add->city . ', PB No: ' . $add->zip_code;

                    if ($add->state == 0 || $add->state == "") {
                        $state = "";
                    } else {
                        $state = $add->statename->name;
                    }

                    $country = $add->countryname->name;
                }

                if ($si->deal_id != 0 && $si->deal_id != "") {
                    $deal_details = SysCrmDeals::where('id', $si->deal_id)->first();
                    if (isset($deal_details)) {

                        if ($deal_details->delivery_address1 == "" && $deal_details->delivery_address2 == "" && $deal_details->delivery_city == "" && $deal_details->delivery_country == "") {

                            $edit = SysCrmDeals::where('id', $si->deal_id)->where('company_id', session('logged_session_data.company_id'))->first();
                            $addressbook = SysCustSupplAddressbook::where('cust_suppl_id', $edit->cust_id)->orderBy('id', 'desc')->first();


                            if ($edit->delivery_company != "") {
                                $update_delivery_company = $edit->delivery_company;
                            } else {
                                $update_delivery_company = $edit->customername->name;
                            }
                            if ($edit->delivery_name != "") {
                                $update_delivery_name = $edit->delivery_name;
                            } else {
                                $update_delivery_name = $edit->cust_name;
                            }
                            if ($edit->delivery_number != "") {
                                $update_delivery_number = $edit->delivery_number;
                            } else {
                                $update_delivery_number = $edit->cust_no;
                            }
                            if ($edit->delivery_email != "") {
                                $update_delivery_email = $edit->delivery_email;
                            } else {
                                $update_delivery_email = $edit->cust_email;
                            }
                            if ($edit->delivery_address2 != "") {
                                $update_delivery_address = $edit->delivery_address2;
                            } else {
                                $update_delivery_address = $addressbook->address2;
                            }
                            if ($edit->delivery_address1 != "") {
                                $update_delivery_address1 = $edit->delivery_address1;
                            } else {
                                $update_delivery_address1 = $addressbook->address;
                            }
                            if ($edit->delivery_address2 != "") {
                                $update_delivery_address2 = $edit->delivery_address2;
                            } else {
                                $update_delivery_address2 = $addressbook->address2;
                            }
                            if ($edit->delivery_city != "") {
                                $update_delivery_city = $edit->delivery_city;
                            } else {
                                $update_delivery_city = $addressbook->city;
                            }
                            if ($edit->delivery_zip_code != "") {
                                $update_delivery_zip_code = $edit->delivery_zip_code;
                            } else {
                                $update_delivery_zip_code = $addressbook->zip_code;
                            }
                            if ($edit->delivery_country != "") {
                                $update_delivery_country = $edit->delivery_country;
                            } else {
                                $update_delivery_country = $addressbook->country;
                            }
                            if ($edit->delivery_state != "") {
                                $update_delivery_state = $edit->delivery_state;
                            } else {
                                $update_delivery_state = $addressbook->state;
                            }

                            DB::table('sys_crm_deals')->where('id', $si->deal_id)->update(
                                [
                                    'delivery_company' => $update_delivery_company,
                                    'delivery_name' => $update_delivery_name,
                                    'delivery_number' => $update_delivery_number,
                                    'delivery_email' => $update_delivery_email,
                                    'delivery_address' => $update_delivery_address,
                                    'delivery_address1' => $update_delivery_address1,
                                    'delivery_address2' => $update_delivery_address2,
                                    'delivery_city' => $update_delivery_city,
                                    'delivery_zip_code' => $update_delivery_zip_code,
                                    'delivery_country' => $update_delivery_country,
                                    'delivery_state' => $update_delivery_state,
                                ]
                            );

                            $deal_details = SysCrmDeals::where('id', $si->deal_id)->first();
                        }


                        /*if($deal_details->delivery_company != "") { $ship_company_name = $deal_details->delivery_company; } else { $ship_company_name = $deal_details->customername->name; }

                        if($deal_details->delivery_address1 != "") { $ship_address1 = $deal_details->delivery_address1; } else { $ship_address1 = ""; }
                        if($deal_details->delivery_address2 != "") { $ship_address2 = $deal_details->delivery_address2; } else { $ship_address2 = ""; }
                        if($deal_details->delivery_city != "") { $delivery_city = $deal_details->delivery_city; } else { $delivery_city = $add->city; }
                        if($deal_details->delivery_zip_code != "") { $delivery_zip_code = $deal_details->delivery_zip_code; } else { $delivery_zip_code = $add->zip_code; }
                        if($deal_details->delivery_country != "") { $delivery_country = $deal_details->country->name; } else  1delivery_country = 1add->countryname->name; */

                        $shipp_email = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_cust_suppl.id', $deal_details->delivery_company)->first();

                        $shipp_trn_no = @$shipp_email->vat_number;


                        $ship_company_name = SysChartofAccounts::where('id', @$si->shipping_supplier)->first()->account_code ?? '';

                        $ship_company_name = SysCustSuppl::where('code', $ship_company_name)->first()->name ?? '';
                  
                        $ship_address1 = @$deal_details->delivery_address1;
                        $ship_address2 = @$deal_details->delivery_address2;
                        $delivery_city = @$deal_details->delivery_city;
                        $delivery_zip_code = @$deal_details->delivery_zip_code;
                        $delivery_country = @$deal_details->country->name;

                        try {
                            if ($deal_details->delivery_state != "") {
                                $delivery_state = $deal_details->state->name;
                            } else {
                                if ($add->state == 0 || $add->state == "") {
                                    $delivery_state = "";
                                } else {
                                    $delivery_state = $add->statename->name;
                                }
                            }
                        } catch (\Throwable $th) {
                            $delivery_state = "";
                        }



                        if ($deal_details->delivery_name != "") {
                            $ship_contact_name = $deal_details->delivery_name;
                        } else {
                            $ship_contact_name = $deal_details->cust_name;
                        }
                        //if($deal_details->delivery_number != "") { $ship_tel = $deal_details->delivery_number; } else { $ship_tel = $deal_details->cust_no; }
                        if ($deal_details->delivery_email != "") {
                            $ship_email = $deal_details->delivery_email;
                        } else {
                            $ship_email = $deal_details->cust_email;
                        }
                    }
                } else {
                    $ship_company_name = @$si->accountname->account_name;
                    $ship_contact_name = $contact_name;
                    $ship_email = $email;
                    //$ship_tel = $tel;
                    //$ship_mob = $mob;
                    $ship_address1 = $add->city . ', PB No: ' . $add->zip_code;
                    $ship_address2 = "";
                    $delivery_city = $add->city . ', PB No: ' . $add->zip_code;
                    $delivery_zip_code = $add->zip_code;
                    $delivery_country = $add->countryname->name;
                    $delivery_state = $add->statename->name;
                }
                //return $ship_address1;

                $ar_data = SysCustSupDetailAr::where('account_id', $si->customer)->get();
                $data = [
                    'si' => $si,
                    'deal_id' => $si->deal_id,
                    'company' => $company,
                    'si_item' => $si_item,
                    'email' => $email,
                    'tel' => $tel,
                    'mob' => $mob,
                    'address' => $address,
                    'address2' => $address2,
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                    'contact_name' => $contact_name,

                    'ship_company_name' => $ship_company_name,
                    'ship_address1' => $ship_address1,
                    'ship_address2' => $ship_address2,
                    'delivery_city' => $delivery_city,
                    'delivery_zip_code' => $delivery_zip_code,
                    'delivery_country' => $delivery_country,
                    'delivery_state' => $delivery_state,

                    'ship_contact_name' => $ship_contact_name,
                    'ship_tel' => $ship_tel,
                    'ship_mob' => $ship_mob,
                    'ship_email' => $ship_email,
                    'cust_trn_no' => $cust_trn_no,
                    'shipp_trn_no' => $shipp_trn_no,
                    'ar_data' => $ar_data,
                ];
                return $data;
            }
            return [];
        } catch (\Throwable $th) {
            return [];
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();

            $staff = SysHelper::get_sales_persons();

            $customer = SysHelper::get_customer_list($company_id);

            $customs_freight_account = SysHelper::get_customs_freight_accounts_for_sales($company_id);
            $supplier = SysHelper::get_supplier_list($company_id);

            $countries = SysCountries::orderby('name', 'asc')->get();
            $states = SysStates::orderby('name', 'asc')->get();
            $items = SysHelper::get_product_list($company_id);

            $customertype = SysCustomerType::orderby('title', 'asc')->get();
            $saletype = SysSaleType::orderby('title', 'asc')->get();


            $cart = SysSalesInvoiceItemsCart::select('sys_sales_invoice_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_sales_invoice_items_cart.part_number')
                ->where('cart_id', session('logged_session_data.cart_id'))->get();

            $adj_list = SysReceiptAdjustments::select('bi_doc_number', 'bi_doc_no', 'bi_total', 'bi_paid', 'bi_balance', 'bi_amount')->wherein('company_id', $company_id)->get();

            $query = SysSalesInvoice::select(DB::raw('sys_sales_invoice.*, (SELECT GROUP_CONCAT(doc_file) FROM sys_sales_invoice_att WHERE siv_id = sys_sales_invoice.id) AS attach, (SELECT GROUP_CONCAT(doc_number) FROM sys_delivery_note WHERE invoice_no = sys_sales_invoice.doc_number) AS dlnno, (SELECT GROUP_CONCAT(doc_number) FROM sys_sales_return WHERE si_doc_number = sys_sales_invoice.doc_number) AS srtno, (SELECT max(debit_amount) FROM sys_chartofaccounts_transaction WHERE transaction_type="salesinvoice" and transaction_no=sys_sales_invoice.doc_number and account_id=sys_sales_invoice.customer) AS amount, (SELECT GROUP_CONCAT(code) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS code'), DB::raw('(SELECT SUM(vatamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_vatamount'), DB::raw('(SELECT SUM(taxableamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_taxableamount'));
            $query->wherein('company_id', $company_id);
            $query->orderby('doc_number', 'desc');
            $salesinvoice = $query->paginate(50);
            $pending_dn = 0;
            $supplier_reference_list = SysHelper::get_supplierlist_charofaccounts();

            $deal_det = "";
            return compact('currency', 'customer', 'customs_freight_account', 'items', 'paymentterms', 'company', 'customertype', 'saletype', 'staff', 'countries', 'states', 'supplier', 'cart', 'salesinvoice', 'pending_dn', 'adj_list', 'supplier_reference_list');
            // return view('backEnd/salesinvoice/manage_sales_invoice', compact('currency', 'customer', 'customs_freight_account', 'items', 'paymentterms','company','customertype','saletype','staff','countries','states','supplier','cart','salesinvoice','pending_dn','adj_list','deal_det','supplier_reference_list'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function add_sales_invoice_items_cart_discount(Request $request)
    {
        try {
            if ($request->discount_amount != "") {
                $qt = SysSalesInvoiceItemsCart::where('cart_id', session('logged_session_data.cart_id'))->get();
                $discount_amount = $request->discount_amount;
                $total = $qt->sum('value');
                foreach ($qt as $t) {
                    $new_discount = ($t->value / $total) * $discount_amount;
                    SysSalesInvoiceItemsCart::where('id', $t->id)->update(
                        [
                            'discount' => $new_discount,
                            'taxableamount' => ($t->unitprice * $t->qty) - $new_discount,
                            'vatamount' => (($t->unitprice * $t->qty) - $new_discount) * $t->tax / 100,
                        ]
                    );
                }
            }
            Toastr::success('Discount Updated Successfully.', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    //normal si cart
    function addsalesinvoiceitemscart_excel(Request $request)
    {
        try {
            DB::beginTransaction();
            $lastSortId = DB::table('sys_sales_invoice_items_cart')->where('cart_id', session('logged_session_data.cart_id'))->max('sort_id');
            $nextSortId = $lastSortId ? $lastSortId + 1 : 1;

            $selected_file = "";
            if (!isset($request->excel_part_no) || count($request->excel_part_no) == 0) {
                Toastr::error('No Data found in excel', 'Failed');
                return redirect()->back();
            }
            if (count($request->excel_part_no) > 0) {
                for ($i = 0; $i < count($request->excel_part_no); $i++) {
                    if ($request->excel_part_no[$i] != "") {
                        $pid = SmItem::where('part_number', $request->excel_part_no[$i])->where('status', 1)->max('id');
                        if ($pid != "") {
                            $description = $request->excel_description[$i];
                            if ($description == false) { //check null value
                                $description = SmItem::where('part_number', $request->excel_part_no[$i])->where('status', 1)->max('description');
                            }

                            $val = ($request->excel_unit_price[$i] * $request->excel_qty[$i]) - $request->excel_discount[$i];
                            $data[] = [
                                'cart_id' => session('logged_session_data.cart_id'),
                                'part_number' => $pid,
                                'description' => $description,
                                'cost' => $request->cost_excel[$i],
                                'tax' => $request->vat_excel[$i],
                                'qty' => $request->excel_qty[$i],
                                'unitprice' => $request->excel_unit_price[$i],
                                'value' => $request->excel_unit_price[$i] * $request->excel_qty[$i],
                                'discount' => $request->excel_discount[$i],
                                'taxableamount' => $val,
                                'vatamount' => $val * $request->vat_excel[$i] / 100,
                                'serialno' => '',
                                'sort_id' => $nextSortId,
                            ];
                            $nextSortId++;
                        } else {
                            DB::rollBack();
                            Toastr::error('Item not found in System ' . $request->excel_part_no[$i], 'Failed');
                            return redirect()->back();
                        }
                    }
                }
            }
            if (count($data) > 0) {
                DB::table('sys_sales_invoice_items_cart')->insert($data);
            }
            DB::commit();
            Toastr::success('Item Imported Successfully', 'Success');
            return redirect()->back();

            /*$ret = SysPurchaseOrderItemsCart::select('sys_purchase_order_items_cart.*','sm_items.part_number AS partno')
            ->join('sm_items','sm_items.id','sys_purchase_order_items_cart.part_number')
            ->where('cart_id',session('logged_session_data.cart_id'))->get();
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }*/
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function addsalesinvoiceitemscart(Request $request)
    {
        try {
            // $check = DB::table('sys_sales_invoice_items_cart')->where(
            // [
            //     'cart_id' => session('logged_session_data.cart_id'),
            //     'part_number' => $request->part_number,                    
            //     'description' => $request->description,
            //     'cost' => ($request->cost === '' ? '0.00' : $request->cost),
            //     'tax' => ($request->tax === '' ? '0.00' : $request->tax),
            //     'qty' => $request->qty,
            //     'unitprice' => $request->unitprice,
            //     'value' => $request->value,
            //     'discount' => $request->discount,
            //     'taxableamount' => $request->taxableamount,
            //     'vatamount' => $request->vatamount,
            //     'serialno' => $request->serialno,
            // ])->count();

            // if($check == 0){
            DB::table('sys_sales_invoice_items_cart')->insert(
                [
                    'cart_id' => session('logged_session_data.cart_id'),
                    'part_number' => $request->part_number,
                    'description' => $request->description,
                    'sort_id' => $request->sort_id,
                    'cost' => ($request->cost === '' ? '0.00' : $request->cost),
                    'tax' => ($request->tax === '' ? '0.00' : $request->tax),
                    'qty' => $request->qty,
                    'unitprice' => $request->unitprice,
                    'value' => $request->value,
                    'discount' => $request->discount,
                    'taxableamount' => $request->taxableamount,
                    'vatamount' => $request->vatamount,
                    'serialno' => $request->serialno,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );
            //}
            $ret = SysSalesInvoiceItemsCart::select('sys_sales_invoice_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_sales_invoice_items_cart.part_number')
                ->where('cart_id', session('logged_session_data.cart_id'))->get();
            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }
    function deletesalesinvoiceitemscart(Request $request)
    {
        try {
            DB::table('sys_sales_invoice_items_cart')->where('id', $request->id)->delete();
            $ret = SysSalesInvoiceItemsCart::select('sys_sales_invoice_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_sales_invoice_items_cart.part_number')
                ->where('cart_id', session('logged_session_data.cart_id'))->get();

            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    function updatesalesinvoiceitemscart(Request $request)
    {
        try {
            DB::table('sys_sales_invoice_items_cart')->where('id', $request->itm_id)->update([
                'part_number' => $request->part_number,
                'sort_id' => $request->sort_id,
                'description' => $request->description,
                'cost' => ($request->cost === '' ? '0.00' : $request->cost),
                'tax' => ($request->tax === '' ? '0.00' : $request->tax),
                'qty' => $request->qty,
                'unitprice' => $request->unitprice,
                'value' => $request->value,
                'discount' => $request->discount,
                'taxableamount' => $request->taxableamount,
                'vatamount' => $request->vatamount,
                'serialno' => $request->serialno,
            ]);

            $ret = SysSalesInvoiceItemsCart::select('sys_sales_invoice_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_sales_invoice_items_cart.part_number')
                ->where('cart_id', session('logged_session_data.cart_id'))->get();

            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = $e;
            return json_encode(array('data' => $ret));
        }
    }

    // normal si cart start

    function getcustomerdetails(Request $request)
    {
        try {

            $ret = SysCustSuppl::select('sys_cust_suppl.*', 'sys_chartofaccounts.id as account_id')
                ->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')
                ->where('sys_chartofaccounts.id', $request->id)->get();

            return json_encode(array('data' => $ret));

        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }
    function getcustomerdetailsarabic(Request $request)
    {
        try {

            $ret = SysCustSupDetailAr::where('account_id', $request->id)->get();

            return json_encode(array('data' => $ret));

        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    function adddealitemstosalesinvoicecart(Request $request)
    {
        //return $request->all();
        try {
            $deal = SysCrmDeals::where('id', $request->si_deal_id)->first();
            $customer_reference = $deal->customername->name;
            $salesman_name = $deal->ownername->full_name;
            $deal_id = $deal->id;
            $tax = SysHelper::get_company_tax($deal->company_id);
            $account_id = SysHelper::get_company_account_id($deal->cust_id);

            /*$deal_items = SysCrmQuoteItems::select('sys_crm_quote_items.*','sm_items.part_number')
            ->join('sm_items','sm_items.id','sys_crm_quote_items.product_id')
            ->where('deal_id',$request->si_deal_id)->where('quote_id',$deal->quote_id)->orderby('sys_crm_quote_items.id','desc')->get();*/

            $deal_items = SysCrmQuoteItems::select('sys_crm_quote_items.*', 'sm_items.part_number', DB::raw('(SELECT sys_crm_quote_items.qty - COALESCE(SUM(qty),0) FROM sys_deal_item_invoiced WHERE deal_id=sys_crm_quote_items.deal_id AND pid=sys_crm_quote_items.product_id) as inv_qty'))
                ->join('sm_items', 'sm_items.id', 'sys_crm_quote_items.product_id')
                ->where('sys_crm_quote_items.deal_id', $request->si_deal_id)->where('sys_crm_quote_items.quote_id', $deal->quote_id)->orderby('sys_crm_quote_items.id', 'desc')->get();

            foreach ($deal_items as $items) {
                $check_cart = SysDealSalesInvoiceItemsCart::where([
                    'cart_id' => session('logged_session_data.cart_id'),
                    'part_number' => $items->product_id,
                    'description' => $request->description,
                    'deal_id' => $items->deal_id,
                    'qty' => $items->inv_qty,
                    'unitprice' => SysHelper::com_curr_format($items->price, 2, '.', ''),
                ])->count();

                if ($check_cart == 0 && $items->inv_qty > 0) {
                    $price = SysHelper::com_curr_format($items->price, 2, '.', '');
                    $discount = SysHelper::com_curr_format($items->discount, 2, '.', '');
                    DB::table('sys_deal_sales_invoice_items_cart')->insert([
                        'cart_id' => session('logged_session_data.cart_id'),
                        'part_number' => $items->product_id,
                        'part_number_txt' => $items->part_number,
                        'description' => $items->description,
                        //'tax' => $tax,
                        'tax' => ($items->vat === '' ? '0.00' : $items->vat),
                        'qty' => $items->inv_qty,
                        'unitprice' => $price,
                        'value' => $price * $items->inv_qty,
                        'discount' => $discount,
                        'fright' => 0.00,
                        'customcharges' => 0.00,
                        'taxableamount' => ($price * $items->inv_qty) - $discount,
                        'vatamount' => (($price * $items->inv_qty) - $discount) * $items->vat / 100,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                        'refid' => $items->id,
                        'deal_id' => $request->si_deal_id,
                    ]);
                }
            }

            return redirect('sales-invoice/create/' . $customer_reference . '/' . $salesman_name . '/' . $account_id . '/' . $deal_id);



        } catch (\Exception $e) {
            return $e;
        }
    }

    public function create2(Request $request)
    {
        try {

            $customer_reference = $request->query('customer_reference');
            $salesman_name = $request->query('salesman_name');
            $deal_id = $request->query('deal_id');
            $deal_code = $request->query('deal_code');


            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $cart_qty = SysDealSalesInvoiceItems::where('created_by', Auth::user()->id)->where('deal_id', $deal_id)->where('cart_id', session('logged_session_data.cart_id'))->sum('qty');
            $si_qty = SysDealSalesInvoiceItems::where('created_by', Auth::user()->id)->where('deal_id', $deal_id)->where('cart_id', session('logged_session_data.cart_id'))->sum('si_qty');

            $deal_acc = SysChartofAccounts::where('account_name', $customer_reference)->where('subgroup2', 7)->where('status', 1)->first();
            $deal_det = SysCrmDeals::where('id', $deal_id)->first();


            $deal_cust = SysCustSuppl::where('name', $customer_reference)->where('catid', 1)->where('status', 1)->first();
            $deal_enduser = SysCrmEndUser::where('deal_id', $deal_id)->where('status', 1)->first();

            $supplier_reference_list = SysHelper::get_supplierlist_charofaccounts();

            if ($cart_qty == $si_qty) {
                Toastr::error('Pending Items Not Found!', 'Failed');
                return redirect('purchase-order/create');
            }

            $select_cart = SysDealSalesInvoiceItems::where('created_by', Auth::user()->id)->where('deal_id', $deal_id)->where('status', 1)->where('cart_id', session('logged_session_data.cart_id'))->get();
            $data = [];
            $check_cart = SysDealSalesInvoiceItemsCart::where(['created_by' => Auth::user()->id, 'status' => 1,])->where('cart_id', session('logged_session_data.cart_id'))->count();

            if ($check_cart == 0) {
                foreach ($select_cart as $items) {
                    $qty = abs($items->qty - $items->si_qty);
                    $data[] = [
                        'cart_id' => session('logged_session_data.cart_id'),
                        'quote_item_id' => $items->quote_item_id,
                        'part_number' => $items->part_number,
                        'part_number_txt' => $items->part_number_txt,
                        'description' => $items->description,
                        'tax' => ($items->tax === '' ? '0.00' : $items->tax),
                        'qty' => $qty,
                        'deal_qty' => $items->deal_qty,
                        'si_qty' => $items->si_qty,
                        'unitprice' => $items->unitprice,
                        'value' => $items->value,
                        'discount' => $items->discount,
                        'fright' => 0,
                        'customcharges' => 0,
                        'taxableamount' => (($items->unitprice * $qty) - $items->discount),
                        'vatamount' => (($items->unitprice * $qty) - $items->discount) * $items->tax / 100,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                        'refid' => $items->item_id,
                        'deal_id' => $items->deal_id,
                    ];
                }
            }

            if (count($data) > 0) {
                SysDealSalesInvoiceItemsCart::where('deal_id', $items->deal_id)->delete();
                SysDealSalesInvoiceItemsCart::insert($data);
            }


            $cart = SysDealSalesInvoiceItemsCart::select('sys_deal_sales_invoice_items_cart.*', 'sys_crm_deal_track.payment_terms', 'sys_crm_deal_track.payment_terms_txt', 'sys_crm_deal_track.reference_no', 'sys_crm_deal_track.reference_date')
                ->join('sys_crm_deal_track', 'sys_crm_deal_track.deal_id', 'sys_deal_sales_invoice_items_cart.deal_id')
                ->where('cart_id', session('logged_session_data.cart_id'))
                ->where('sys_deal_sales_invoice_items_cart.created_by', Auth::user()->id)
                ->where('sys_deal_sales_invoice_items_cart.deal_id', $deal_id)
                ->where('sys_deal_sales_invoice_items_cart.status', 1)->orderby('sys_deal_sales_invoice_items_cart.id', 'asc')->get();

            $part_number_text = [];

            foreach ($cart as $c) {
                $part_number_text[] = $c->part_number_txt;
            }




            $supplier_name = SysCrmDealTrackApprovalPurchease::where('deal_id', $deal_id)->max('supplier_name');

            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();

            $staff = SysHelper::get_sales_persons();


            $customer_det = SysCustSuppl::select('sys_cust_suppl.*')
                ->join('sys_crm_deals', 'sys_crm_deals.cust_id', 'sys_cust_suppl.id')
                ->where('sys_crm_deals.id', $select_cart[0]->deal_id)->first();
            $net_vat = $customer_det->vat_percentage;

            //$customer = SysHelper::get_customer_list($company_id);
            $customer = SysChartofAccounts::select('id', 'account_name', 'account_code')
                ->where('account_name', $customer_reference)->where('subgroup2', 7)->where('status', 1)->orderby('account_name', 'asc')->get();

            $supplier = SysHelper::get_supplier_list($company_id);

            $customs_freight_account = SysHelper::get_customs_freight_accounts_for_sales($company_id);

            $countries = SysCountries::orderby('name', 'asc')->get();
            $states = SysStates::orderby('name', 'asc')->get();
            $items = SysHelper::get_product_list($company_id);
            $customertype = SysCustomerType::orderby('title', 'asc')->get();
            $saletype = SysSaleType::orderby('title', 'asc')->get();


            // $deal_enduser = SysCrmEndUser::select('end_user_company_name','address_line_a','end_user_contact_person','mobile_no','email','device_serial')->where('deal_id',$deal_id)->first();

            $deal_details = SysCrmDeals::select('delivery_company', 'delivery_name', 'delivery_number', 'delivery_email', 'delivery_address', 'owner', 'deal_discount', 'quote_id', 'deal_currency')->where('id', $deal_id)->first();

            $edit_cfc = SysCrmQuoteCharges::where('deal_id', $deal_id)->where('quote_id', $deal_details->quote_id)->get();

            $account_id = $customer->where('account_name', $customer_reference)->max('id');

            $adj_list = SysReceiptAdjustments::select('bi_doc_number', 'bi_doc_no', 'bi_total', 'bi_paid', 'bi_balance', 'bi_amount')->wherein('company_id', $company_id)->get();

            $query = SysSalesInvoice::select(DB::raw('sys_sales_invoice.*, (SELECT GROUP_CONCAT(doc_file) FROM sys_sales_invoice_att WHERE siv_id = sys_sales_invoice.id) AS attach, (SELECT GROUP_CONCAT(doc_number) FROM sys_delivery_note WHERE invoice_no = sys_sales_invoice.doc_number) AS dlnno, (SELECT GROUP_CONCAT(doc_number) FROM sys_sales_return WHERE si_doc_number = sys_sales_invoice.doc_number) AS srtno, (SELECT max(debit_amount) FROM sys_chartofaccounts_transaction WHERE transaction_type="salesinvoice" and transaction_no=sys_sales_invoice.doc_number and account_id=sys_sales_invoice.customer) AS amount, (SELECT GROUP_CONCAT(code) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS code'), DB::raw('(SELECT SUM(vatamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_vatamount'), DB::raw('(SELECT SUM(taxableamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_taxableamount'));
            $query->wherein('company_id', $company_id);
            $query->orderby('doc_number', 'desc');
            $salesinvoice = $query->paginate(50);
            $pending_dn = 0;

            return view('backEnd/salesinvoice/manage_sales_invoice', compact('currency', 'customer', 'customs_freight_account', 'items', 'paymentterms', 'company', 'customertype', 'saletype', 'staff', 'countries', 'states', 'cart', 'account_id', 'customer_det', 'supplier', 'net_vat', 'supplier_name', 'deal_details', 'deal_enduser', 'edit_cfc', 'salesinvoice', 'pending_dn', 'adj_list', 'deal_acc', 'deal_det', 'deal_cust', 'supplier_reference_list', 'part_number_text'));

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function create2_exe($customer_reference = null, $salesman_name = null, $account_id = null, $deal_id = null)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $cart_qty = SysDealSalesInvoiceItemsCart::where('created_by', Auth::user()->id)->where('deal_id', $deal_id)->where('cart_id', session('logged_session_data.cart_id'))->sum('qty');
            $si_qty = SysDealSalesInvoiceItemsCart::where('created_by', Auth::user()->id)->where('deal_id', $deal_id)->where('cart_id', session('logged_session_data.cart_id'))->sum('si_qty');

            if ($cart_qty == $si_qty) {
                Toastr::error('Pending Items Not Found!', 'Failed');
                return redirect('sales-invoice/create');
            }

            $select_cart = SysDealSalesInvoiceItemsCart::select('sys_deal_sales_invoice_items_cart.*', 'sys_crm_deal_track.payment_terms', 'sys_crm_deal_track.reference_no', 'sys_crm_deal_track.reference_date')
                ->join('sys_crm_deal_track', 'sys_crm_deal_track.deal_id', 'sys_deal_sales_invoice_items_cart.deal_id')
                ->where('cart_id', session('logged_session_data.cart_id'))
                ->where('sys_deal_sales_invoice_items_cart.created_by', Auth::user()->id)
                ->where('sys_deal_sales_invoice_items_cart.deal_id', $deal_id)
                ->where('sys_deal_sales_invoice_items_cart.status', 1)->get();

            $supplier_name = SysCrmDealTrackApprovalPurchease::where('deal_id', $deal_id)->max('supplier_name');

            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();

            $staff = SysHelper::get_sales_persons();

            $customer_det = SysCustSuppl::select('sys_cust_suppl.*')
                ->join('sys_crm_deals', 'sys_crm_deals.cust_id', 'sys_cust_suppl.id')
                ->where('sys_crm_deals.id', $select_cart[0]->deal_id)->first();
            $net_vat = $customer_det->vat_percentage;

            $customer = SysHelper::get_customer_list($company_id);

            $supplier = SysHelper::get_supplier_list($company_id);
            $customs_freight_account = SysHelper::get_customs_freight_accounts_for_sales($company_id);

            $countries = SysCountries::orderby('name', 'asc')->get();
            $states = SysStates::orderby('name', 'asc')->get();
            $items = SysHelper::get_product_list($company_id);
            $customertype = SysCustomerType::orderby('title', 'asc')->get();
            $saletype = SysSaleType::orderby('title', 'asc')->get();


            $enduser_details = SysCrmEndUser::select('end_user_company_name', 'address_line_a', 'end_user_contact_person', 'mobile_no', 'email')->where('deal_id', $deal_id)->first();

            $deal_details = SysCrmDeals::select('delivery_company', 'delivery_name', 'delivery_number', 'delivery_email', 'delivery_address', 'owner', 'deal_discount', 'quote_id')->where('id', $deal_id)->first();

            $edit_cfc = SysCrmQuoteCharges::where('deal_id', $deal_id)->where('quote_id', $deal_details->quote_id)->get();

            return view('backEnd/salesinvoice/manage_sales_invoice2', compact('currency', 'customer', 'customs_freight_account', 'items', 'paymentterms', 'company', 'customertype', 'saletype', 'staff', 'countries', 'states', 'select_cart', 'account_id', 'customer_det', 'supplier', 'net_vat', 'supplier_name', 'deal_details', 'enduser_details', 'edit_cfc'));

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function additems(Request $request)
    {
        try {
            $ret = SysSalesInvoiceItems::select('doc_number', 'id')->where('customer', $request->id)->get();

            return json_encode(array('data' => $ret));

        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    function getproformainvoiceforsi(Request $request)
    {
        try {
            $ret = SysProformaInvoice::select('doc_number', 'id')->where('customer', $request->id)->where('company_id', session('logged_session_data.company_id'))->get();
            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    function getproformainvoiceitemsforsi(Request $request)
    {
        try {
            $ret = SysProformaInvoiceItems::select('sm_items.part_number as partnumber', 'sm_items.description', 'sys_proforma_invoice_items.*', 'sys_proforma_invoice.payment_terms', 'sys_proforma_invoice.sales_man', 'sys_proforma_invoice.currency', 'sys_proforma_invoice.delivery_terms', 'sys_proforma_invoice.shipping_name', 'sys_proforma_invoice.shipping_address', 'sys_proforma_invoice.customer_country', 'sys_proforma_invoice.customer_state', 'sys_proforma_invoice.customer_type', 'sys_proforma_invoice.sale_type', 'sys_proforma_invoice.end_user_name', 'sys_proforma_invoice.contact_person_name', 'sys_proforma_invoice.contact_person_email', 'sys_proforma_invoice.contact_person_no')
                ->join('sys_proforma_invoice', 'sys_proforma_invoice.id', 'sys_proforma_invoice_items.profo_id')
                ->join('sm_items', 'sm_items.id', 'sys_proforma_invoice_items.part_number')
                ->where('profo_id', $request->qt_id)->get();

            return response()->json([$ret]);

        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    public function getcustomername(Request $request)
    {
        $input = $request->all();

        try {
            // $vendors_query = SysCustSuppl::select('id','code','name')->where('catid',1); // 1 customers, 2 suppliers
            // if(Auth::user()->role_id != 1){
            //     $vendors_query->where('created_by', Auth::user()->id);
            // }
            // $vendors = $vendors_query->get();
            $customers = SysCustSuppl::select('sys_cust_suppl.id', 'sys_cust_suppl.contcat_person', 'sys_cust_suppl.vat_country', 'sys_cust_suppl.payment_terms', 'sys_cust_suppl.address', 'sys_countries.name')
                ->leftjoin('sys_countries', 'sys_countries.id', 'sys_cust_suppl.vat_country')->where('sys_cust_suppl.id', $request->id)->get();
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

    public function store(Request $request)
    {
        if ($request->customer == "") {
            Toastr::error('Customer not found', 'Failed');
            return redirect()->back();
        }
        // if($request->currency ==""){Toastr::error('Currency not found', 'Failed'); return redirect()->back();}
        // if($request->sales_man ==""){Toastr::error('Sales Man not found', 'Failed'); return redirect()->back();}
        // if($request->delivery_terms ==""){Toastr::error('Delivery Terms not found', 'Failed'); return redirect()->back();}
        // if($request->payment_terms ==""){Toastr::error('Payment Terms not found', 'Failed'); return redirect()->back();}


        $chkdeal = SysHelper::get_dealid_from_code($request->deal_id);
        $chkdealif = SysSalesInvoice::where('deal_id', $chkdeal)->get();
        // if(count($chkdealif)>0){
        //     Toastr::error('Already created Invoice for this Deal ID', 'Failed');
        //     return redirect()->back();
        // }



        try {
            DB::beginTransaction();
            $si = new SysSalesInvoice();
            $si->doc_number = SysHelper::get_new_sales_invoice_code();
            $si->doc_date = Carbon::createFromFormat('d/m/Y', $request->doc_date)->format('Y-m-d');
            $si->customer = $request->customer;
            $si->currency = $request->currency;
            $si->printed_invoice_number = $request->printed_invoice_number;
            $si->lpo_number = $request->reference_no;
            $si->lpo_date = Carbon::createFromFormat('d/m/Y', $request->reference_date)->format('Y-m-d');
            $si->payment_terms = $request->payment_terms;
            $si->payment_terms2 = $request->payment_terms2;
            $si->delivery_terms = $request->delivery_terms;
            $si->sales_man = $request->sales_man;
            $si->narration = $request->narration;


            $si->shipping_address = $request->shipping_address_1;
            $si->shipping_name = $request->shipping_name;
            $si->shipping_supplier = $request->shipping_supplier;
            $si->shipping_contact_no = $request->shipping_contact_no;
            $si->shipping_email = $request->shipping_email;




            $si->customer_type = $request->customer_type;
            $si->sale_type = $request->sale_type;
            $si->customer_country = $request->customer_country;
            $si->customer_state = $request->customer_state;

            $si->end_user_name = $request->end_user_name;
            $si->contact_person_name = $request->contact_person_name;
            $si->contact_person_email = $request->contact_person_email;
            $si->contact_person_no = $request->contact_person_no;

            $si->net_vat = $request->net_vat ?: 0;
            $si->deal_id = SysHelper::get_dealid_from_code($request->deal_id);

            $si->narration = $request->narration;
            $si->supplier_name = $request->supplier_name;
            $si->ref_supplier_id = $request->ref_supplier_id ? implode(',', $request->ref_supplier_id) : null;
            $si->vat_percent = $request->vat_percent ?: 0;
            $si->vat_number = $request->vat_number ?: '';
            $si->company_id = session('logged_session_data.company_id');
            $si->created_by = Auth::user()->id;
            $si->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');


           
                $si->device_serial = $request->has('device_serial') && $request->device_serial !== ''
                    ? $request->device_serial
                    : null;
            




            $si->status = 1;


            $si->save();
            $si->toArray();


            if (session('logged_session_data.company_id') == 8 || session('logged_session_data.company_id') == 10) {
                SysHelper::save_account_arabic_details($request->customer, $request->company_name_ar, $request->contact_person_ar, $request->address_ar);
            }

            $total_tax_amount = array_sum(
                array_map(function ($value) {
                    return (float) str_replace(',', '', $value);
                }, $request->taxableamount)
            );
            $total_vat_amount = array_sum(
                array_map(function ($value) {
                    return (float) str_replace(',', '', $value);
                }, $request->vatamount)
            );

            //customer account cr
            SysHelper::trn_chartof_accounts_transaction($request->customer, $si->id, $si->doc_number, $si->doc_date, 'salesinvoice', ($total_tax_amount + $total_vat_amount), '0.00', '', 1, 0, "", 1);

            //sales account dr 
            $sales_account_id = SysHelper::get_sales_account_id();
            SysHelper::trn_chartof_accounts_transaction($sales_account_id, $si->id, $si->doc_number, $si->doc_date, 'salesinvoice', '0.00', ($total_tax_amount), '', 1, 0, "", 1);

            //vat account dr 
            $sales_vat_account_id = SysHelper::get_sales_vat_account_id();
            SysHelper::trn_chartof_accounts_transaction($sales_vat_account_id, $si->id, $si->doc_number, $si->doc_date, 'salesinvoice', '0.00', ($total_vat_amount), '', 1, 0, "", 1);

            for ($i = 0; $i < count($request->part_number); $i++) {
                if ($request->part_number[$i] != "") {
                    $sii = new SysSalesInvoiceItems();
                    $sii->si_id = $si->id;
                    $sii->part_number = $request->part_number[$i];
                    $sii->description = $request->description[$i];
                    $sii->cost = (float) str_replace(',', '', $request->cost[$i] ?? 0);
                    $sii->tax = ($request->tax[$i] === '' ? '0.00' : $request->tax[$i]);
                    $sii->qty = $request->qty[$i];
                    $sii->unitprice = (float) str_replace(',', '', $request->unitprice[$i] ?? 0);
                    $sii->value = (float) str_replace(',', '', $request->value[$i] ?? 0);
                    $sii->discount = (float) str_replace(',', '', $request->discount[$i] ?? 0);
                    $sii->taxableamount = (float) str_replace(',', '', $request->taxableamount[$i] ?? 0);
                    $sii->vatamount = (float) str_replace(',', '', $request->vatamount[$i] ?? 0);
                    $sii->serialno = $request->serial_no[$i];
                    $sii->sort_id = $request->sort_id[$i];
                    $sii->status = 1;
                    $sii->created_by = Auth::user()->id;
                    $sii->save();

                    $str_arr = explode(",", $request->serial_no[$i]);
                    foreach ($str_arr as $srl) {
                        $values = array('si_id' => $si->id, 'part_number' => $request->part_number[$i], 'srl_no' => $srl);
                        DB::table('sys_sales_invoice_items_srl')->insert($values);
                    }
                }
            }

            for($i = 0; $i < count($request->cfc_name); $i++) {
                if($request->cfc_name[$i] !="" && $request->cfc_credit_account[$i] !="" && $request->cfc_amount[$i] !=""){
                    $cfc = new SysSalesInvoiceCFCharges();
                    $cfc->si_id = $si->id;
                    $cfc->si_doc_number = $si->doc_number;
                    $cfc->date = $request->cfc_date[$i] ? SysHelper::normalizeToYmd($request->cfc_date[$i]) : null;
                    $cfc->bill_number = $request->cfc_bill_no[$i];
                    $cfc->cfc_name = $request->cfc_name[$i];
                    $cfc->cfc_credit_account = $request->cfc_credit_account[$i];
                    $cfc->cfc_amount = str_replace(',', '', $request->cfc_amount[$i]);
                    $cfc->cfc_remarks = $request->cfc_remarks[$i];
                    $cfc->status = 1;
                    $cfc->created_by = Auth::user()->id;
                    $cfc->save();

        

                    //Supplier account cr
                    SysHelper::trn_chartof_accounts_transaction($request->cfc_credit_account[$i],$si->id,$si->doc_number,$si->doc_date,'salesinvoice','0.00',str_replace(',', '', $request->cfc_amount[$i]),$request->cfc_remarks[$i],1,0,"",$i+2);

                    //Direct Exp account dr Customs Fright
                    SysHelper::trn_chartof_accounts_transaction($request->cfc_name[$i],$si->id,$si->doc_number,$si->doc_date,'salesinvoice',str_replace(',', '', $request->cfc_amount[$i]),'0.00',$request->cfc_remarks[$i],1,0,"",$i+2);

                }
            }

            $this->syncDeliveryNoteChargesFromSalesInvoice($si);

            $adjData = db::table('sys_sales_invoice_adjustment_temp')->where('cart_id', session('logged_session_data.cart_id'))
                ->where('company_id', session('logged_session_data.company_id'))
                ->where('user_id', Auth::user()->id)
                ->where('status', 1)
                ->get();
            if (count($adjData) > 0) {

                $adj_temp_data = [];
                $adj_ret_data = [];
                for ($i = 0; $i < count($adjData); $i++) {
                    if ($adjData[$i]->set_amt != 0) {

                        $rec = SysReceipt::where('doc_number', $adjData[$i]->receiptno)->first();

                        if (isset($rec)) {
                            $adjusted_amt = SysReceiptAdjustments::select(db::raw('COALESCE(max(bi_cheque_amount)-sum(bi_paid),0) as adjusted_amt'))->where('bi_doc_number', $adjData[$i]->receiptno)->value('adjusted_amt');
                            if ($rec->mode == 1) {
                                $transaction_type = "cashreceipt";
                            } else {
                                $transaction_type = "bankreceipt";
                            }
                            $currency = $rec->currency;
                            $exe_type = "receipt";
                        } else {
                            $adjusted_amt = 0;
                            $rec = SysSalesReturn::where('doc_number', $adjData[$i]->receiptno)->first();

                            if (isset($rec)) {
                                $currency = $rec->currency;
                                $transaction_type = "";
                                $exe_type = "return";
                            } else {
                                $currency = 1;
                                $exe_type = "openingbalance";
                                $transaction_type = "";
                                $exe_type = "receipt";
                            }
                        }

                        if ($adjData[$i]->set_amt_act == $adjData[$i]->set_amt) {
                            $bi_balance_to_adjust = 0;
                            $bi_extra_amount = 0;
                        }
                        if ($adjData[$i]->set_amt_act > $adjData[$i]->set_amt) {
                            $bi_balance_to_adjust = 0;
                            $bi_extra_amount = $adjData[$i]->set_amt_act - $adjData[$i]->set_amt;
                        }
                        if ($adjData[$i]->set_amt_act < $adjData[$i]->set_amt) {
                            $bi_balance_to_adjust = $adjData[$i]->set_amt - $adjData[$i]->set_amt_act;
                            $bi_extra_amount = 0;
                        }

                        if ($exe_type == "receipt") {
                            if ($adjData[$i]->set_amt == $adjusted_amt) {
                                $amt_bi_balance_to_adjust = 0;
                            }
                            if ($adjData[$i]->set_amt > $adjusted_amt) {
                                $amt_bi_balance_to_adjust = $adjData[$i]->set_amt > $adjusted_amt;
                            }
                            if ($adjData[$i]->set_amt < $adjusted_amt) {
                                $amt_bi_balance_to_adjust = $adjusted_amt - $adjData[$i]->set_amt;
                            }
                            $adj_temp_data[] = [
                                'transaction_type' => $transaction_type,
                                'bi_cheque_amount' => $adjData[$i]->set_amt_act,
                                'bi_amount_adjusted' => $adjData[$i]->set_amt,
                                'bi_balance_to_adjust' => $amt_bi_balance_to_adjust,
                                'bi_extra_amount' => $bi_extra_amount,
                                'bi_currency' => @$currency,
                                'bi_doc_number' => $adjData[$i]->receiptno,
                                'bi_contains' => '',
                                'bi_doc_no' => $si->doc_number,
                                'bi_lpo_no' => '',
                                'bi_doc_date' => $si->doc_date,
                                'bi_total' => $sii->sum('taxableamount') + $sii->sum('vatamount'),
                                'bi_paid' => $adjData[$i]->set_amt,
                                'bi_balance' => $amt_bi_balance_to_adjust,
                                'bi_extra_amount' => $bi_extra_amount,
                                'bi_amount' => $adjData[$i]->set_amt,
                                'bi_narration' => "Adjusted from SI No: " . $si->doc_number,
                                'account_id' => $si->customer,
                                'status' => 1,
                                'created_from' => 1,
                                'created_by' => Auth::user()->id,
                                'created_at' => Carbon::now('+04:00'),
                                'company_id' => session('logged_session_data.company_id'),
                            ];
                        }
                        if ($exe_type == "return") {
                            $adj_ret_data = [
                                'srn_no' => $adjData[$i]->receiptno,
                                'dln_no' => @$rec->dn_doc_number,
                                'siv_no' => $si->doc_number,
                                'lpo_number' => @$rec->lpo_number,
                                'doc_date' => $si->doc_date,
                                'total_amount' => $adjData[$i]->set_amt_act,
                                'paid_amount' => $adjData[$i]->set_amt,
                                'balance_amount' => $adjData[$i]->set_amt_act - $adjData[$i]->set_amt,
                                'narration' => 'Adjusted from SI No: ' . $si->doc_number,
                                'status' => 1,
                                'created_by' => Auth::user()->id,
                            ];
                        }
                    }
                }
                if (count($adj_temp_data) > 0) {
                    SysReceiptAdjustments::insert($adj_temp_data);
                }
                if (count($adj_ret_data) > 0) {
                    SysSalesReturnAdjestment::insert($adj_ret_data);
                }

                $adjData = db::table('sys_sales_invoice_adjustment_temp')->where('cart_id', session('logged_session_data.cart_id'))
                    ->where('company_id', session('logged_session_data.company_id'))
                    ->where('user_id', Auth::user()->id)
                    ->where('status', 1)
                    ->delete();


            }

            SysCrmDeals::where('id', $si->deal_id)->update(['sales_invoice_id' => $si->id]);
            SysSalesInvoiceItemsCart::where('cart_id', session('logged_session_data.cart_id'))->delete();
            DB::table('sys_sales_invoice_att')->where('cart_id', session('logged_session_data.cart_id'))->where('siv_id', 0)->where('company_id', session('logged_session_data.company_id'))->update(['siv_id' => $si->id]);

            if (isset($request->create_deal)) {
                if ($request->create_deal == 1) {
                    $ret = $this->generateDeal($si->id);
                    if ($ret[0] == "OK") {
                        $retTrack = $this->generateDealTrack($ret[1]);
                        if ($retTrack != "TRACK") {
                            return $retTrack;
                        }
                    }
                    if ($ret[0] != "OK") {
                        DB::rollback();
                        return $ret[1];
                    }
                }
            }
            if (isset($request->create_dn)) {
                if ($request->create_dn == 1) {
                    $retDN = $this->generateDeliveryNote($si->id);
                    if ($retDN != "DN") {
                        DB::rollback();
                        return $retDN;
                    }
                }
            }

            $deal_item = SysDealSalesInvoiceItems::where('cart_id', session('logged_session_data.cart_id'))->where('deal_id', $si->deal_id)->get();

            foreach ($deal_item as $ditm) {
                DB::table('sys_crm_quote_items')
                    ->where('deal_id', $si->deal_id)
                    ->where('id', $ditm->quote_item_id)
                    ->increment('si_qty', $ditm->qty);
            }
            SysDealSalesInvoiceItemsCart::where('cart_id', session('logged_session_data.cart_id'))->delete();
            SysDealSalesInvoiceItems::where('cart_id', session('logged_session_data.cart_id'))->delete();

            DB::commit();

            Toastr::success('Operation successful', 'Success');

            if ($request->has('deal_track_page')) {
                $trackid = SysCrmDealTrack::where('deal_id', $si->deal_id)->first();
                return redirect('crm-deal-track-approval-list/' . $trackid->id);   // 👉 redirect if hidden input exists
            }

            return redirect('sales-invoice/' . $si->id);
            //return redirect()->back();

        } catch (\Exception $e) {

            DB::rollback();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function store2(Request $request)
    {
        //return $request->all();        
        if ($request->customer == "") {
            Toastr::error('Customer not found', 'Failed');
            return redirect()->back();
        }
        // if($request->currency ==""){Toastr::error('Currency not found', 'Failed'); return redirect()->back();}
        // if($request->sales_man ==""){Toastr::error('Sales Man not found', 'Failed'); return redirect()->back();}
        // if($request->delivery_terms ==""){Toastr::error('Delivery Terms not found', 'Failed'); return redirect()->back();}
        // if($request->payment_terms ==""){Toastr::error('Payment Terms not found', 'Failed'); return redirect()->back();}

        if ($request->part_number[0] != "none" && $request->qty[0] != "" && $request->unitprice[0] != "") {

        } else {
            Toastr::error('Items not found', 'Failed');
            return redirect()->back();
        }

        $chkdeal = SysHelper::get_dealid_from_code($request->deal_id);
        $chkdealif = SysSalesInvoice::where('deal_id', $chkdeal)->get();
        // if(count($chkdealif)>0){
        //     Toastr::error('Already created Invoice for this Deal ID', 'Failed');
        //     return redirect()->back();
        // }

        try {
            DB::beginTransaction();
            if (count(array_filter($request->part_number)) > 0 && count(array_filter($request->qty)) > 0 && count(array_filter($request->unitprice)) > 0) {
                $si = new SysSalesInvoice();
                $si->doc_number = SysHelper::get_new_sales_invoice_code();
                $si->doc_date = date('Y-m-d', strtotime($request->doc_date));
                $si->customer = $request->customer;
                $si->currency = $request->currency;
                $si->printed_invoice_number = $request->printed_invoice_number;
                $si->lpo_number = $request->reference_no;
                $si->lpo_date = $request->reference_date;
                $si->payment_terms = $request->payment_terms;
                $si->payment_terms2 = $request->payment_terms2;
                $si->delivery_terms = $request->delivery_terms;
                $si->sales_man = $request->sales_man;
                $si->narration = $request->narration;
                $si->shipping_name = $request->shipping_name;
                $si->shipping_address = $request->shipping_address;
                $si->customer_type = $request->customer_type;
                $si->sale_type = $request->sale_type;
                $si->customer_country = $request->customer_country;
                $si->customer_state = $request->customer_state;
                $si->end_user_name = $request->end_user_name;
                $si->contact_person_name = $request->contact_person_name;
                $si->contact_person_email = $request->contact_person_email;
                $si->contact_person_no = $request->contact_person_no;

                try {
                    $si->net_vat = ($request->net_vat === '' ? '0.00' : $request->net_vat);
                } catch (\Throwable $th) {
                    $si->net_vat = 0;
                }

                $si->deal_id = SysHelper::get_dealid_from_code($request->deal_id);
                $si->narration = $request->narration;
                $si->supplier_name = $request->supplier_name;
                $si->company_id = session('logged_session_data.company_id');
                $si->created_by = Auth::user()->id;
                $si->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                $si->status = 1;
                $si->deal_discount = $request->deal_discount;
                $si->save();
                $si->toArray();

                $total_deal_discount = 0;
                if ($request->deal_discount != "") {
                    $total_deal_discount = $request->deal_discount;
                }

                $total_cfc_amount = array_sum($request->cfc_amount);


                $total_tax_amount = array_sum(
                    array_map(function ($value) {
                        return (float) str_replace(',', '', $value);
                    }, $request->taxableamount)
                );
                $total_vat_amount = array_sum(
                    array_map(function ($value) {
                        return (float) str_replace(',', '', $value);
                    }, $request->vatamount)
                );

                $deal_discount = $total_deal_discount;
                $deal_discount_vat = $total_deal_discount * $request->vat[0] / 100;
                $deal_discount_inc_vat = $deal_discount + $deal_discount_vat;

                //customer account cr
                SysHelper::trn_chartof_accounts_transaction($request->customer, $si->id, $si->doc_number, $si->doc_date, 'salesinvoice', ($total_tax_amount + $total_vat_amount - $deal_discount_inc_vat), '0.00', '', 1, 0, "", 1);

                //sales account dr 
                $sales_account_id = SysHelper::get_sales_account_id();
                SysHelper::trn_chartof_accounts_transaction($sales_account_id, $si->id, $si->doc_number, $si->doc_date, 'salesinvoice', '0.00', ($total_tax_amount - $deal_discount), '', 1, 0, "", 1);

                //vat account dr 
                $sales_vat_account_id = SysHelper::get_sales_vat_account_id();
                SysHelper::trn_chartof_accounts_transaction($sales_vat_account_id, $si->id, $si->doc_number, $si->doc_date, 'salesinvoice', '0.00', ($total_vat_amount - $deal_discount_vat), '', 1, 0, "", 1);

                for ($i = 0; $i < count($request->part_number); $i++) {
                    if ($request->part_number[$i] != "" && $request->qty[$i] != "" && $request->unitprice[$i] != "") {
                        if ($request->qty[$i] > 0) {
                            $sii = new SysSalesInvoiceItems();
                            $sii->si_id = $si->id;
                            $sii->part_number = $request->part_number[$i];
                            $sii->description = $request->description[$i];
                            $sii->tax = ($request->vat[$i] === '' ? '0.00' : $request->vat[$i]);
                            $sii->qty = $request->qty[$i];
                            $sii->unitprice = $request->unitprice[$i];
                            $sii->value = $request->value[$i];
                            $sii->discount = ($request->discount[$i] === '' ? '0.00' : $request->discount[$i]);
                            $sii->taxableamount = ($request->taxamount[$i] === '' ? '0.00' : $request->taxamount[$i]);
                            $sii->vatamount = ($request->vatamount[$i] === '' ? '0.00' : $request->vatamount[$i]);
                            $sii->status = 1;
                            $sii->created_by = Auth::user()->id;
                            $sii->save();
                            DB::table('sys_deal_item_invoiced')->insert([
                                'deal_id' => $si->deal_id,
                                'invoice_id' => $si->id,
                                'pid' => $request->part_number[$i],
                                'qty' => $request->qty[$i],
                                'status' => 1,
                                'created_by' => Auth::user()->id,
                            ]);


                            $quote_item_id = SysCrmDeals::select('quote_id')->where('id', $si->deal_id)->first();
                            $crm_quote_item = SysCrmQuoteItems::where('quote_id', $quote_item_id->quote_id)->where('deal_id', $si->deal_id)->where('product_id', $request->part_number[$i])->first();
                            if (isset($crm_quote_item)) {
                                SysCrmQuoteItems::where('quote_id', $quote_item_id->quote_id)->where('deal_id', $si->deal_id)->where('product_id', $request->part_number[$i])->update(
                                    ['si_qty' => $crm_quote_item->si_qty + $request->qty[$i],]
                                );
                            }

                        }
                    }
                }

                for ($i = 0; $i < count($request->cfc_name); $i++) {
                    if ($request->cfc_name[$i] != "" && $request->cfc_credit_account[$i] != "" && $request->cfc_amount[$i] != "") {
                        $cfc = new SysSalesInvoiceCFCharges();
                        $cfc->si_id = $si->id;
                        $cfc->si_doc_number = $si->doc_number;
                        $cfc->cfc_name = $request->cfc_name[$i];
                        $cfc->cfc_credit_account = $request->cfc_credit_account[$i];
                        $cfc->cfc_amount = $request->cfc_amount[$i];
                        $cfc->cfc_remarks = $request->cfc_remarks[$i];
                        $cfc->status = 1;
                        $cfc->created_by = Auth::user()->id;
                        $cfc->save();

                        //Supplier account cr
                        SysHelper::trn_chartof_accounts_transaction($request->cfc_credit_account[$i], $si->id, $si->doc_number, $si->doc_date, 'salesinvoice', '0.00', $request->cfc_amount[$i], $request->cfc_remarks[$i], 1, 0, "", $i + 2);

                        //Direct Exp account dr Customs Fright
                        SysHelper::trn_chartof_accounts_transaction($request->cfc_name[$i], $si->id, $si->doc_number, $si->doc_date, 'salesinvoice', $request->cfc_amount[$i], '0.00', $request->cfc_remarks[$i], 1, 0, "", $i + 2);

                    }
                }
                $this->syncDeliveryNoteChargesFromSalesInvoice($si);

                SysCrmDeals::where('id', SysHelper::get_dealid_from_code($request->deal_id))->update(['sales_invoice_id' => $si->id]);
                SysDealSalesInvoiceItemsCart::where('sys_deal_sales_invoice_items_cart.created_by', Auth::user()->id)->where('sys_deal_sales_invoice_items_cart.deal_id', SysHelper::get_dealid_from_code($request->deal_id))->where('sys_deal_sales_invoice_items_cart.status', 1)->delete();

                $deal_track_id = SysCrmDealTrack::select('id')->where('deal_id', $si->deal_id)->first();

                DB::commit();
                Toastr::success('Operation successful', 'Success');
                return redirect('crm-deal-track-approval/' . $deal_track_id->id);
            } else {
                Toastr::error('Operation Failed. please enter valid data', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            return $e;
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    //end store method 

    public function calcTotal($amt, $tax, $cfc)
    {
        return ($amt / $tax * $cfc) + $amt;
    }

    public function show(Request $request, $id)
    {
        try {
            $si = SysSalesInvoice::find($id);
            $si_items = SysSalesInvoiceItems::where('si_id', '=', $si->id)->get();
            $si_att = SysSalesInvoiceAttachment::where('si_id', '=', $si->id)->get();
            $company = SysCompany::find($si->company_id);
            $cfcharges = SysSalesInvoiceCFCharges::where('si_id', '=', $si->id)->get();

            return view('backEnd/salesinvoice/sales_invoice_view', compact('si', 'si_items', 'si_att', 'company', 'cfcharges'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function find(Request $request)
    {
        try {
            $si = SysSalesInvoice::where('doc_number', 'like', '%' . $request->si_number . '%')->first();

            if ($si != '') {
                $si_items = SysSalesInvoiceItems::where('si_id', '=', $si->id)->get();
                $si_att = SysSalesInvoiceAttachment::where('si_id', '=', $si->id)->get();
                $company = SysCompany::find($si->company_id);
                $cfcharges = SysSalesInvoiceCFCharges::where('si_id', '=', $si->id)->get();
                return view('backEnd/salesinvoice/sales_invoice_view', compact('si', 'si_items', 'si_att', 'company', 'cfcharges'));
            } else {
                Toastr::error('Invalid SI Number', 'Failed');
                return redirect('purchase-invoice');
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function print($id)
    {
        $si = SysSalesInvoice::find($id);
        if (!empty($si)) {
            $company = SysCompany::find($si->company_id);
            $si_item = SysSalesInvoiceItems::where('si_id', '=', $si->id)->get();
            $sup_email = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_chartofaccounts.id', $si->vendors)->first();
            $address = SysCustSupplAddressbook::where('set_default', 1)->where('cust_suppl_id', $sup_email->id)->first();
            //return $po_item;

            if (!empty($address)) {
                $address = $address->address;
                $address2 = $address->address2;
                $city = $address->city;
                $state = $address->statename->name;
                $country = $address->countryname->name;

            } else {
                $address = "";
                $address2 = "";
                $city = "";
                $state = "";
                $country = "";
            }

            $data = [
                'si' => $si,
                'company' => $company,
                'si_item' => $si_item,
                'email' => $sup_email->email,
                'address' => $address,
                'address2' => $address2,
                'city' => $city,
                'state' => $state,
                'country' => $country,
            ];
            $pdf = PDF::loadView('backEnd.pdf_print.si_pdf', $data);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download($si->doc_number . '-' . $si->accountname->name . ".pdf");
        } else {
            return "error!!";
            //return view('web.syscom_credit_application_form');
        }
    }
    public function printpreview($id)
    {
        $si = SysSalesInvoice::find($id);
        if (!empty($si)) {
            $company = SysCompany::find($si->company_id);
            $si_item = SysSalesInvoiceItems::where('si_id', '=', $si->id)->get();
            //return $po_item;
            $data = [
                'si' => $si,
                'company' => $company,
                'si_item' => $si_item,
            ];
            $pdf = PDF::loadView('backEnd.pdf_print.si_pdf', $data);
            //$pdf->setPaper('A4', 'portrait');
            // //return $pdf->download("purchase_invoice_".$si->doc_number.".pdf");
            return $pdf->stream("sales_invoice_" . $si->doc_number . ".pdf");
            //return view('backEnd/pdf_print/si_pdf', compact('si','si_item','company'));

        } else {
            return "error!!";
            //return view('web.syscom_credit_application_form');
        }
    }

    public function addattachment(Request $request)
    {
        //return $request;

        $si_attach_file = "";
        if ($request->file('si_attach_file') != "") {
            $file = $request->file('si_attach_file');
            $si_attach_file = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('public/uploads/si_attachment/', $si_attach_file);
            $si_attach_file = 'public/uploads/si_attachment/' . $si_attach_file;
        }

        try {
            $si_att = new SysSalesInvoiceAttachment();
            $si_att->si_id = $request->si_id;
            $si_att->file_name = $request->file_name;
            $si_att->description = $request->description;
            $si_att->validtill = date('Y-m-d', strtotime($request->validtill));
            $si_att->si_attach_file = $si_attach_file;
            $si_att->status = 1;
            $si_att->created_by = Auth::user()->id;
            $results = $si_att->save();

            Toastr::success('Operation successful', 'Success');
            return redirect('sales-invoice/' . $si_att->si_id);
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $currency = SysCurrencySettings::select('id', 'code', 'ex_rate')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();

            $staff = SysHelper::get_sales_persons();

            //$customer = SysHelper::get_customer_list_all($company_id);

            $customs_freight_account = SysHelper::get_customs_freight_accounts_for_sales($company_id);
            $supplier = SysHelper::get_supplier_list_all($company_id);

            $countries = SysCountries::orderby('name', 'asc')->get();
            $states = SysStates::orderby('name', 'asc')->get();
            $items = SysHelper::get_product_list($company_id);
            $customertype = SysCustomerType::orderby('title', 'asc')->get();
            $saletype = SysSaleType::orderby('title', 'asc')->get();


            $edit_si = SysSalesInvoice::find($id);
            $currencylist2 = DB::table('sys_currency_rate as r')->select('r.id', 'r.from_currency', 'r.to_currency', 'c.code', 'r.rate')
                ->join('sys_currency as c', 'c.id', 'r.to_currency')
                ->where('r.status', 1)->where('r.from_currency', $edit_si->currency)
                ->orderBy('c.code', 'ASC')->get();
            $edit_si_items = SysSalesInvoiceItems::where('si_id', '=', $edit_si->id)->orderBy('sort_id')->get();
            $edit_cfc = SysSalesInvoiceCFCharges::where('si_id', '=', $edit_si->id)->get();
            $edit_company = SysCompany::find($edit_si->company_id);
            $edit_ar = SysCustSupDetailAr::where('account_id', $edit_si->customer)->first();

            $customer = SysChartofAccounts::select('id', 'account_name', 'account_code')->where('id', $edit_si->customer)->get();

            // $cart = SysSalesInvoiceItemsCart::select('sys_sales_invoice_items_cart.*','sm_items.part_number AS partno','sm_items.description')
            // ->join('sm_items','sm_items.id','sys_sales_invoice_items_cart.part_number')
            // ->where('cart_id',session('logged_session_data.cart_id'))
            // ->where('si_id',$edit_si->id)->get();
            // return  $cart;


            $adjusted_amt = SysReceiptAdjustments::where('bi_doc_no', $edit_si->doc_number)->sum('bi_amount_adjusted');
            $adjusted_amt_actual = $edit_si_items->sum('taxableamount') + $edit_si_items->sum('vatamount');
            $adjusted_amt = $adjusted_amt_actual - $adjusted_amt;



            $list_of_unadjusted = SysHelper::get_list_of_unadjusted([$edit_si->customer], $company_id);
            $list_of_unadjusted_pdc = SysHelper::get_list_of_unadjusted_pdc([$edit_si->customer], $company_id);




            $receiptAdjustments = SysReceiptAdjustments::where('bi_doc_no', $edit_si->doc_number)->where('status', 1)->get();
            $returnAdjustments = SysSalesReturnAdjestment::where('siv_no', $edit_si->doc_number)->where('status', 1)->get();


            $adj_list = SysReceiptAdjustments::select('bi_doc_number', 'bi_doc_no', 'bi_doc_date', 'bi_lpo_no', 'bi_total', 'bi_paid', 'bi_balance', 'bi_amount', 'bi_balance_to_adjust', 'bi_cheque_amount', 'bi_amount_adjusted')->wherein('company_id', $company_id)->where('bi_doc_no', $edit_si->doc_number)->get();

            $query = SysSalesInvoice::select(DB::raw('sys_sales_invoice.*, (SELECT GROUP_CONCAT(doc_file) FROM sys_sales_invoice_att WHERE siv_id = sys_sales_invoice.id) AS attach, (SELECT GROUP_CONCAT(doc_number) FROM sys_delivery_note WHERE invoice_no = sys_sales_invoice.doc_number) AS dlnno, (SELECT GROUP_CONCAT(doc_number) FROM sys_sales_return WHERE si_doc_number = sys_sales_invoice.doc_number) AS srtno, (SELECT max(debit_amount) FROM sys_chartofaccounts_transaction WHERE transaction_type="salesinvoice" and transaction_no=sys_sales_invoice.doc_number and account_id=sys_sales_invoice.customer) AS amount, (SELECT GROUP_CONCAT(code) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS code'), DB::raw('(SELECT SUM(vatamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_vatamount'), DB::raw('(SELECT SUM(taxableamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_taxableamount'));
            $query->wherein('company_id', $company_id);
            $query->orderby('doc_number', 'desc');
            $salesinvoice = $query->paginate(50);
            $pending_dn = 0;


            $supplier_reference_list = SysHelper::get_supplierlist_charofaccounts();


            return compact('currency', 'currencylist2', 'customer', 'customs_freight_account', 'items', 'paymentterms', 'company', 'customertype', 'saletype', 'staff', 'countries', 'states', 'supplier', 'edit_si', 'edit_si_items', 'edit_cfc', 'edit_ar', 'adjusted_amt', 'adjusted_amt_actual', 'receiptAdjustments', 'returnAdjustments', 'list_of_unadjusted', 'list_of_unadjusted_pdc', 'salesinvoice', 'pending_dn', 'adj_list', 'supplier_reference_list');
            // return view('backEnd/salesinvoice/manage_sales_invoice_edit', compact('currency','currencylist2', 'customer', 'customs_freight_account', 'items', 'paymentterms','company','customertype','saletype','staff','countries','states','supplier','edit_si','edit_si_items','edit_cfc','edit_ar','adjusted_amt','adjusted_amt_actual','receiptAdjustments','returnAdjustments','list_of_unadjusted','list_of_unadjusted_pdc','salesinvoice','pending_dn','adj_list'));

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function view($id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();

            $staff = SysHelper::get_sales_persons();

            $customer = SysHelper::get_customer_list_all($company_id);
            $customs_freight_account = SysHelper::get_customs_freight_accounts_for_sales($company_id);
            $supplier = SysHelper::get_supplier_list_all($company_id);

            $countries = SysCountries::orderby('name', 'asc')->get();
            $states = SysStates::orderby('name', 'asc')->get();
            $items = SysHelper::get_product_list($company_id);
            $customertype = SysCustomerType::orderby('title', 'asc')->get();
            $saletype = SysSaleType::orderby('title', 'asc')->get();


            $edit_si = SysSalesInvoice::find($id);
            $edit_si_items = SysSalesInvoiceItems::where('si_id', '=', $edit_si->id)->get();
            $edit_cfc = SysSalesInvoiceCFCharges::where('si_id', '=', $edit_si->id)->get();
            $edit_company = SysCompany::find($edit_si->company_id);
            $edit_ar = SysCustSupDetailAr::where('account_id', $edit_si->customer)->first();

            // $cart = SysSalesInvoiceItemsCart::select('sys_sales_invoice_items_cart.*','sm_items.part_number AS partno','sm_items.description')
            // ->join('sm_items','sm_items.id','sys_sales_invoice_items_cart.part_number')
            // ->where('cart_id',session('logged_session_data.cart_id'))
            // ->where('si_id',$edit_si->id)->get();
            // return  $cart;

            $receiptAdjustments = SysReceiptAdjustments::where('bi_doc_no', $edit_si->doc_number)->where('status', 1)->get();

            return view('backEnd/salesinvoice/manage_sales_invoice_view', compact('currency', 'customer', 'customs_freight_account', 'items', 'paymentterms', 'company', 'customertype', 'saletype', 'staff', 'countries', 'states', 'supplier', 'edit_si', 'edit_si_items', 'edit_cfc', 'edit_ar', 'receiptAdjustments'));

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function download($id, $type = null)
    {
        try {
            $address = "";
            $address2 = "";
            $city = "";
            $state = "";
            $country = "";
            $contact_name = "";
            $email = "";
            $tel = "";
            $mob = "";
            $ship_company_name = "";
            $delivery_city = "";
            $delivery_zip_code = "";
            $delivery_country = "";
            $delivery_state = "";
            $cust_trn_no = "";
            $shipp_trn_no = "";

            $si = SysSalesInvoice::find($id);
            if (!empty($si)) {
                $company = SysCompany::find($si->company_id);
                $si_item = SysSalesInvoiceItems::where('si_id', '=', $si->id)->get();
                $sup_email = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_chartofaccounts.id', $si->customer)->first();
                if (!empty($sup_email)) {
                    $add = SysCustSupplAddressbook::where('cust_suppl_id', $sup_email->id)->first();
                }

                $contact_name = $sup_email->customer_salutation . ' ' . $sup_email->first_name . ' ' . $sup_email->last_name;
                $email = $sup_email->email;
                $tel = $sup_email->contcat_number;
                $mob = $sup_email->mobile;

                $ship_tel = $tel;
                $ship_mob = $mob;

                $cust_trn_no = $sup_email->vat_number;
                //$shipp_trn_no = $sup_email->vat_number;

                if (!empty($add)) {
                    $address = $add->address;
                    $address2 = $add->address2;
                    $city = $add->city . ', PB No: ' . $add->zip_code;

                    if ($add->state == 0 || $add->state == "") {
                        $state = "";
                    } else {
                        $state = $add->statename->name;
                    }

                    $country = $add->countryname->name;
                }

                if ($si->deal_id != 0 && $si->deal_id != "") {
                    $deal_details = SysCrmDeals::where('id', $si->deal_id)->first();
                    if (isset($deal_details)) {

                        if ($deal_details->delivery_address1 == "" && $deal_details->delivery_address2 == "" && $deal_details->delivery_city == "" && $deal_details->delivery_country == "") {

                            $edit = SysCrmDeals::where('id', $si->deal_id)->where('company_id', session('logged_session_data.company_id'))->first();
                            $addressbook = SysCustSupplAddressbook::where('cust_suppl_id', $edit->cust_id)->orderBy('id', 'desc')->first();

                            if ($edit->delivery_company != "") {
                                $update_delivery_company = $edit->delivery_company;
                            } else {
                                $update_delivery_company = $edit->customername->name;
                            }
                            if ($edit->delivery_name != "") {
                                $update_delivery_name = $edit->delivery_name;
                            } else {
                                $update_delivery_name = $edit->cust_name;
                            }
                            if ($edit->delivery_number != "") {
                                $update_delivery_number = $edit->delivery_number;
                            } else {
                                $update_delivery_number = $edit->cust_no;
                            }
                            if ($edit->delivery_email != "") {
                                $update_delivery_email = $edit->delivery_email;
                            } else {
                                $update_delivery_email = $edit->cust_email;
                            }
                            if ($edit->delivery_address2 != "") {
                                $update_delivery_address = $edit->delivery_address2;
                            } else {
                                $update_delivery_address = $addressbook->address2;
                            }
                            if ($edit->delivery_address1 != "") {
                                $update_delivery_address1 = $edit->delivery_address1;
                            } else {
                                $update_delivery_address1 = $addressbook->address;
                            }
                            if ($edit->delivery_address2 != "") {
                                $update_delivery_address2 = $edit->delivery_address2;
                            } else {
                                $update_delivery_address2 = $addressbook->address2;
                            }
                            if ($edit->delivery_city != "") {
                                $update_delivery_city = $edit->delivery_city;
                            } else {
                                $update_delivery_city = $addressbook->city;
                            }
                            if ($edit->delivery_zip_code != "") {
                                $update_delivery_zip_code = $edit->delivery_zip_code;
                            } else {
                                $update_delivery_zip_code = $addressbook->zip_code;
                            }
                            if ($edit->delivery_country != "") {
                                $update_delivery_country = $edit->delivery_country;
                            } else {
                                $update_delivery_country = $addressbook->country;
                            }
                            if ($edit->delivery_state != "") {
                                $update_delivery_state = $edit->delivery_state;
                            } else {
                                $update_delivery_state = $addressbook->state;
                            }

                            DB::table('sys_crm_deals')->where('id', $si->deal_id)->update(
                                [
                                    'delivery_company' => $update_delivery_company,
                                    'delivery_name' => $update_delivery_name,
                                    'delivery_number' => $update_delivery_number,
                                    'delivery_email' => $update_delivery_email,
                                    'delivery_address' => $update_delivery_address,
                                    'delivery_address1' => $update_delivery_address1,
                                    'delivery_address2' => $update_delivery_address2,
                                    'delivery_city' => $update_delivery_city,
                                    'delivery_zip_code' => $update_delivery_zip_code,
                                    'delivery_country' => $update_delivery_country,
                                    'delivery_state' => $update_delivery_state,
                                ]
                            );

                            $deal_details = SysCrmDeals::where('id', $si->deal_id)->first();
                        }


                        /*if($deal_details->delivery_company != "") { $ship_company_name = $deal_details->delivery_company; } else { $ship_company_name = $deal_details->customername->name; }

                        if($deal_details->delivery_address1 != "") { $ship_address1 = $deal_details->delivery_address1; } else { $ship_address1 = ""; }
                        if($deal_details->delivery_address2 != "") { $ship_address2 = $deal_details->delivery_address2; } else { $ship_address2 = ""; }
                        if($deal_details->delivery_city != "") { $delivery_city = $deal_details->delivery_city; } else { $delivery_city = $add->city; }
                        if($deal_details->delivery_zip_code != "") { $delivery_zip_code = $deal_details->delivery_zip_code; } else { $delivery_zip_code = $add->zip_code; }
                        if($deal_details->delivery_country != "") { $delivery_country = $deal_details->country->name; } else  1delivery_country = 1add->countryname->name; */

                        $shipp_email = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_cust_suppl.id', $deal_details->delivery_company)->first();

                        $shipp_trn_no = @$shipp_email->vat_number;


                        $ship_company_name = SysChartofAccounts::where('id', @$si->shipping_supplier)->first()->account_code ?? '';

                        $ship_company_name = SysCustSuppl::where('code', $ship_company_name)->first()->name ?? '';

                        

                        $ship_address1 = @$deal_details->delivery_address1;
                        $ship_address2 = @$deal_details->delivery_address2;
                        $delivery_city = @$deal_details->delivery_city;
                        $delivery_zip_code = @$deal_details->delivery_zip_code;
                        $delivery_country = @$deal_details->country->name;

                        try {
                            if ($deal_details->delivery_state != "") {
                                $delivery_state = $deal_details->state->name;
                            } else {
                                if ($add->state == 0 || $add->state == "") {
                                    $delivery_state = "";
                                } else {
                                    $delivery_state = $add->statename->name;
                                }
                            }
                        } catch (\Throwable $th) {
                            $delivery_state = "";
                        }



                        if ($deal_details->delivery_name != "") {
                            $ship_contact_name = $deal_details->delivery_name;
                        } else {
                            $ship_contact_name = $deal_details->cust_name;
                        }
                        //if($deal_details->delivery_number != "") { $ship_tel = $deal_details->delivery_number; } else { $ship_tel = $deal_details->cust_no; }
                        if ($deal_details->delivery_email != "") {
                            $ship_email = $deal_details->delivery_email;
                        } else {
                            $ship_email = $deal_details->cust_email;
                        }
                    }
                } else {
                    $ship_company_name = @$si->accountname->account_name;
                    $ship_contact_name = $contact_name;
                    $ship_email = $email;
                    //$ship_tel = $tel;
                    //$ship_mob = $mob;
                    $ship_address1 = $add->city . ', PB No: ' . $add->zip_code;
                    $ship_address2 = "";
                    $delivery_city = $add->city . ', PB No: ' . $add->zip_code;
                    $delivery_zip_code = $add->zip_code;
                    $delivery_country = $add->countryname->name;
                    $delivery_state = $add->statename->name;
                }
                //return $ship_address1;

                $ar_data = SysCustSupDetailAr::where('account_id', $si->customer)->get();
                $data = [
                    'si' => $si,
                    'company' => $company,
                    'si_item' => $si_item,
                    'email' => $email,
                    'tel' => $tel,
                    'mob' => $mob,
                    'address' => $address,
                    'address2' => $address2,
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                    'contact_name' => $contact_name,

                    'ship_company_name' => $ship_company_name,
                    'ship_address1' => $ship_address1,
                    'ship_address2' => $ship_address2,
                    'delivery_city' => $delivery_city,
                    'delivery_zip_code' => $delivery_zip_code,
                    'delivery_country' => $delivery_country,
                    'delivery_state' => $delivery_state,

                    'ship_contact_name' => $ship_contact_name,
                    'ship_tel' => $ship_tel,
                    'ship_mob' => $ship_mob,
                    'ship_email' => $ship_email,
                    'cust_trn_no' => $cust_trn_no,
                    'shipp_trn_no' => $shipp_trn_no,
                    'ar_data' => $ar_data,
                ];

                //return view('backEnd.pdf_print.si_pdf', $data);

                if ($si->company_id == 8 || $si->company_id == 10) {
                    //return SysHelper::convertAmountToWordsArabic(1034566.92,'Riyal','Baiza');
                    return view('backEnd.pdf_print.si_ar_pdf', $data);
                    $pdf = PDF::loadView('backEnd.pdf_print.si_ar_pdf', $data);
                    //$pdf = PDF::loadView('backEnd.pdf_print.si_ar_pdf', $data);
                    //return $pdf->stream();


                } else if ($si->company_id == 4) {
                    $pdf = PDF::loadView('backEnd.pdf_print.si_co_uk_pdf', $data);
                } else if ($si->company_id == 7 || $si->company_id == 9 || $si->company_id == 12) {
                    $pdf = PDF::loadView('backEnd.pdf_print.si_uk_pdf', $data);
                } else {

                    if ($type == "t") {
                        //return view('backEnd.pdf_print.si_normal_pdf', $data);
                        $pdf = PDF::loadView('backEnd.pdf_print.si_normal_pdf', $data);
                    } else {

                        try {
                            $pdf = PDF::loadView('backEnd.pdf_print.si_pdf', $data);
                            $originalPath = storage_path('app/public/original_invoice.pdf');
                            $pdf->save($originalPath);

                            require_once base_path('vendor/setasign/fpdi/autoload.php');

                            $sourcePath = storage_path('app/public/original_invoice.pdf'); // your original PDF path
                            $pdf = new Fpdi();

                            // Load the source PDF
                            $pageCount = $pdf->setSourceFile($sourcePath);

                            for ($i = 1; $i <= $pageCount; $i++) {
                                // Import current page once
                                $templateId = $pdf->importPage($i);

                                // Add the same page twice
                                for ($j = 0; $j < 2; $j++) {
                                    $pdf->AddPage();
                                    $pdf->useTemplate($templateId);

                                    // Optional: Mark the copy for debugging or info
                                    $pdf->SetFont('Arial', '', 10);
                                    $pdf->SetTextColor(150, 150, 150);
                                    //$pdf->Text(10, 10, "Page {$i} copy " . ($j + 1));
                                }
                            }

                            $filename = $si->doc_number . '-' . $si->accountname->account_name . '.pdf';
                            $outputPath = public_path($filename);

                            $pdf->Output('F', $outputPath);
                            return response()->download($outputPath);

                        } catch (\Throwable $th) {
                            return $th;
                        }
                    }

                }
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download($si->doc_number . '-' . $si->accountname->account_name . ".pdf");
            } else {
                return "error!!";
                //return view('web.syscom_credit_application_form');
            }
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function addsalesinvoiceitems(Request $request)
    {
        try {
            DB::table('sys_sales_invoice_items')->insert(
                [
                    //'cart_id' => session('logged_session_data.cart_id'),
                    'si_id' => $request->si_id,
                    'part_number' => $request->part_number,
                    'description' => $request->description,
                    'tax' => ($request->tax === '' ? '0.00' : $request->tax),
                    'qty' => $request->qty,
                    'unitprice' => $request->unitprice,
                    'value' => $request->value,
                    'discount' => $request->discount,
                    'taxableamount' => $request->taxableamount,
                    'vatamount' => $request->vatamount,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );

            $ret = SysSalesInvoiceItems::select('sys_sales_invoice_items.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_sales_invoice_items.part_number')->where('si_id', $request->si_id)->get();

            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    function updatesalesinvoiceitems(Request $request)
    {
        try {
            DB::table('sys_sales_invoice_items')->where('id', $request->itm_id)->update([
                'part_number' => $request->part_number,
                'description' => $request->description,
                'tax' => ($request->tax === '' ? '0.00' : $request->tax),
                'qty' => $request->qty,
                'unitprice' => $request->unitprice,
                'value' => $request->value,
                'discount' => $request->discount,
                'taxableamount' => $request->taxableamount,
                'vatamount' => $request->vatamount,
            ]);

            $ret = SysSalesInvoiceItems::select('sys_sales_invoice_items.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_sales_invoice_items.part_number')->where('si_id', $request->si_id)->get();

            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = $e;
            return json_encode(array('data' => $ret));
        }
    }

    function deletesalesinvoiceitems(Request $request)
    {
        try {
            DB::table('sys_sales_invoice_items')->where('id', $request->id)->delete();
            $ret = SysSalesInvoiceItems::select('sys_sales_invoice_items.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_sales_invoice_items.part_number')->where('si_id', $request->si_id)->get();

            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    function sales_invoice_discount_update(Request $request)
    {
        try {

            DB::table('sys_sales_invoice')->where('id', $request->id)->update(['deal_discount' => $request->deal_discount]);

        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $si = SysSalesInvoice::find($request->id);
            $si->doc_date = Carbon::createFromFormat('d/m/Y', $request->doc_date)->format('Y-m-d');
            if ($request->doc_number != $request->doc_number_main) {
                $exists = SysSalesInvoice::where('doc_number', $request->doc_number)->exists();
                if ($exists) {
                    DB::rollback();
                    Toastr::error('Operation Failed. Document number already exists', 'Failed');
                    return redirect()->back();
                }
                $si->doc_number = $request->doc_number;
            }
            $si->customer = $request->customer;
            $si->currency = $request->currency;
            $si->printed_invoice_number = $request->printed_invoice_number;
            $si->lpo_number = $request->reference_no;
            $si->lpo_date = Carbon::createFromFormat('d/m/Y', $request->reference_date)->format('Y-m-d');
            $si->payment_terms = $request->payment_terms;
            $si->payment_terms2 = $request->payment_terms2;
            $si->delivery_terms = $request->delivery_terms;
            $si->sales_man = $request->sales_man;
            $si->narration = $request->narration;


            $si->shipping_name = $request->shipping_name;
            $si->shipping_address = $request->shipping_address;



            $si->shipping_address = $request->shipping_address_1;
            $si->shipping_name = $request->shipping_name;

            $si->shipping_supplier = $request->shipping_supplier;
            $si->shipping_contact_no = $request->shipping_contact_no;
            $si->shipping_email = $request->shipping_email;

            $si->customer_type = $request->customer_type;
            $si->sale_type = $request->sale_type;
            $si->customer_country = $request->customer_country;
            $si->customer_state = $request->customer_state;
            $si->end_user_name = $request->end_user_name;
            $si->contact_person_name = $request->contact_person_name;
            $si->contact_person_email = $request->contact_person_email;
            $si->contact_person_no = $request->contact_person_no;
            $si->net_vat = $request->net_vat ?: 0;
            $si->deal_id = SysHelper::get_dealid_from_code($request->deal_id);
            $si->narration = $request->narration;
            $si->supplier_name = $request->supplier_name;


            $si->vat_number = $request->vat_number ?: '';
            $si->vat_percent = $request->vat_percent ?: 0;

            $si->ref_supplier_id = $request->ref_supplier_id ? implode(',', $request->ref_supplier_id) : null;




            $si->device_serial = $request->has('device_serial') && $request->device_serial !== ''
                ? $request->device_serial
                : null;


            $si->status = 1;
            $si->company_id = session('logged_session_data.company_id');
            $si->updated_by = Auth::user()->id;
            $si->updated_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $si->save();
            $si->toArray();

            SysSalesInvoiceItems::where('si_id', $si->id)->delete();
            DB::table('sys_sales_invoice_items_srl')->where('si_id', $si->id)->delete();


            for ($i = 0; $i < count($request->part_number); $i++) {


                if ($request->part_number[$i] != "") {
                    $sii = new SysSalesInvoiceItems();
                    $sii->si_id = $si->id;
                    $sii->part_number = $request->part_number[$i];
                    $sii->description = $request->description[$i];
                    $sii->cost = (float) str_replace(',', '', $request->cost[$i] ?? 0);
                    $sii->tax = ($request->tax[$i] === '' ? '0.00' : $request->tax[$i]);
                    $sii->qty = $request->qty[$i];
                    $sii->unitprice = (float) str_replace(',', '', $request->unitprice[$i] ?? 0);
                    $sii->value = (float) str_replace(',', '', $request->value[$i] ?? 0);
                    $sii->discount = (float) str_replace(',', '', $request->discount[$i] ?? 0);
                    $sii->taxableamount = (float) str_replace(',', '', $request->taxableamount[$i] ?? 0);
                    $sii->vatamount = (float) str_replace(',', '', $request->vatamount[$i] ?? 0);
                    $sii->serialno = $request->serial_no[$i];
                    $sii->sort_id = $request->sort_id[$i];
                    $sii->status = 1;
                    $sii->created_by = Auth::user()->id;
                    $sii->save();



                    $str_arr = explode(",", $request->serial_no[$i]);
                    foreach ($str_arr as $srl) {
                        $values = array('si_id' => $si->id, 'part_number' => $request->part_number[$i], 'srl_no' => $srl);
                        DB::table('sys_sales_invoice_items_srl')->insert($values);
                    }

                }
            }



            if (session('logged_session_data.company_id') == 8 || session('logged_session_data.company_id') == 10) {
                SysHelper::save_account_arabic_details($request->customer, $request->company_name_ar, $request->contact_person_ar, $request->address_ar);
            }



            $sales_invoice_items = SysSalesInvoiceItems::where('si_id', $si->id)->get();
            $total_tax_amount = $sales_invoice_items->sum('taxableamount');
            $total_vat_amount = $sales_invoice_items->sum('vatamount');

            $vat = $sales_invoice_items->max('tax');
            $deal_discount = $si->deal_discount;
            $deal_discount_vat = $si->deal_discount * $vat / 100;
            $deal_discount_inc_vat = $deal_discount + $deal_discount_vat;

            //DB::table('sys_sales_invoice_items')->where('si_id',$si->id)->delete();
            DB::table('sys_sales_invoice_cf_charges')->where('si_id', $si->id)->delete();
            DB::table('sys_chartofaccounts_transaction')->where('transaction_no', $si->doc_number)->where('transaction_type', 'salesinvoice')->delete();

            //customer account cr
            SysHelper::trn_chartof_accounts_transaction($request->customer, $si->id, $si->doc_number, $si->doc_date, 'salesinvoice', ($total_tax_amount + $total_vat_amount - $deal_discount_inc_vat), '0.00', '', 1, 0, "", 1);

            //sales account dr 
            $sales_account_id = SysHelper::get_sales_account_id();
            SysHelper::trn_chartof_accounts_transaction($sales_account_id, $si->id, $si->doc_number, $si->doc_date, 'salesinvoice', '0.00', ($total_tax_amount - $deal_discount), '', 1, 0, "", 1);

            //vat account dr 
            $sales_vat_account_id = SysHelper::get_sales_vat_account_id();
            SysHelper::trn_chartof_accounts_transaction($sales_vat_account_id, $si->id, $si->doc_number, $si->doc_date, 'salesinvoice', '0.00', ($total_vat_amount - $deal_discount_vat), '', 1, 0, "", 1);


            for($i = 0; $i < count($request->cfc_name); $i++) {
                if($request->cfc_name[$i] !="" && $request->cfc_credit_account[$i] !="" && $request->cfc_amount[$i] !=""){
                    $cfc = new SysSalesInvoiceCFCharges();
                    $cfc->si_id = $si->id;
                    $cfc->si_doc_number = $si->doc_number;
                    $cfc->date = $request->cfc_date[$i] ? SysHelper::normalizeToYmd($request->cfc_date[$i]) : null;

                    $cfc->bill_number = $request->cfc_bill_no[$i];
                    $cfc->cfc_name = $request->cfc_name[$i];
                    $cfc->cfc_credit_account = $request->cfc_credit_account[$i];
                    $cfc->cfc_amount = str_replace(',', '', $request->cfc_amount[$i]);
                    $cfc->cfc_remarks = $request->cfc_remarks[$i];
                    $cfc->status = 1;
                    $cfc->created_by = Auth::user()->id;
                    $cfc->save();

                    //Supplier account cr
                    SysHelper::trn_chartof_accounts_transaction($request->cfc_credit_account[$i],$si->id,$si->doc_number,$si->doc_date,'salesinvoice','0.00',str_replace(',', '', $request->cfc_amount[$i]),$request->cfc_remarks[$i],1,0,"",$i+2);

                    //Direct Exp account dr Customs Fright
                    SysHelper::trn_chartof_accounts_transaction($request->cfc_name[$i],$si->id,$si->doc_number,$si->doc_date,'salesinvoice',str_replace(',', '', $request->cfc_amount[$i]),'0.00',$request->cfc_remarks[$i],1,0,"",$i+2);

                }
            }

            $this->syncDeliveryNoteChargesFromSalesInvoice($si);


            if (isset($request->create_deal)) {
                if ($request->create_deal == 1) {
                    $ret = $this->generateDeal_update($si->id);
                    if ($ret[0] == "OK") {
                        //$retTrack = $this->generateDealTrack_update($ret[1]);
                        //if($retTrack!="TRACK"){
                        //    return $retTrack;
                        //}
                    }
                    if ($ret[0] != "OK") {
                        //DB::rollback();
                        //return $ret[1];
                    }
                }
            }
            if (isset($request->create_dn)) {
                if ($request->create_dn == 1) {
                    $retDN = $this->generateDeliveryNote_update($si->id);
                    if ($retDN != "DN") {
                        //DB::rollback();
                        //return $retDN;
                    }
                }
            }


            DB::commit();

            if ($request->btnSubmit == "p") {
                Toastr::success('Operation successful', 'Success');
                return redirect('sales-invoice/' . $request->id . '/download');
            }

            Toastr::success('Operation successful', 'Success');
            return redirect('sales-invoice/' . $request->id);

        } catch (\Exception $e) {

            return $e;

            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    private function syncDeliveryNoteChargesFromSalesInvoice($si)
    {
        if (empty($si) || empty($si->id)) {
            return;
        }

        // Ensure linked DN records keep SI linkage so DN screens can always resolve CFC from SI.
        $linkedDnIds = collect();
        if (!empty($si->dn_id)) {
            $linkedDnIds->push((int) $si->dn_id);
        }

        $dnByInvoice = SysDeliveryNote::where('invoice_no', $si->doc_number)->pluck('id');
        if ($dnByInvoice && $dnByInvoice->count() > 0) {
            $linkedDnIds = $linkedDnIds->merge($dnByInvoice->map(function ($id) {
                return (int) $id;
            }));
        }

        $linkedDnIds = $linkedDnIds->filter(function ($id) {
            return $id > 0;
        })->unique()->values();

        foreach ($linkedDnIds as $dnId) {
            $dn = SysDeliveryNote::find($dnId);
            if (empty($dn)) {
                continue;
            }

            $existing = collect(explode(',', (string) ($dn->ref_si_id ?? '')))
                ->map(function ($id) {
                    return (int) trim($id);
                })
                ->filter(function ($id) {
                    return $id > 0;
                })
                ->unique()
                ->values();

            if (!$existing->contains((int) $si->id)) {
                $existing->push((int) $si->id);
            }

            $dn->ref_si_id = $existing->implode(',');
            $dn->save();

            // Mirror SI CFC rows to DN-local key so DN edit always shows current values.
            $dnLocalDocKey = 'DN-' . (int) $dn->id;
            DB::table('sys_sales_invoice_cf_charges')->where('si_id', 0)->where('si_doc_number', $dnLocalDocKey)->delete();

            $siCharges = SysSalesInvoiceCFCharges::where('si_id', (int) $si->id)
                ->where('status', 1)
                ->get();

            foreach ($siCharges as $row) {
                $cfc = new SysSalesInvoiceCFCharges();
                $cfc->si_id = 0;
                $cfc->si_doc_number = $dnLocalDocKey;
                $cfc->date = $row->date;
                $cfc->bill_number = $row->bill_number;
                $cfc->cfc_name = $row->cfc_name;
                $cfc->cfc_credit_account = $row->cfc_credit_account;
                $cfc->cfc_amount = $row->cfc_amount;
                $cfc->cfc_remarks = $row->cfc_remarks;
                $cfc->status = 1;
                $cfc->created_by = Auth::user()->id;
                $cfc->save();
            }
        }
    }

    public function destroy(Request $request, SmQuotation $smQuotation)
    {

        try {
            $result = SmQuotation::destroy($request->id);

            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect('quotations');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect('quotations');
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            DB::table('sys_sales_invoice')->where('id', $id)->update(['status' => 2]);
            DB::table('sys_sales_invoice_items')->where('si_id', $id)->update(['status' => 2]);
            DB::table('sys_sales_invoice_cf_charges')->where('si_id', $id)->update(['status' => 2]);
            DB::table('sys_chartofaccounts_transaction')->where('transaction_id', $id)->where('transaction_type', 'salesinvoice')->update(['status' => 2]);
            //DB::table('sys_crm_deals')->where('sales_invoice_id',$id)->update(['sales_invoice_id' => 0]);

            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function restore($id)
    {
        try {
            DB::beginTransaction();

            DB::table('sys_sales_invoice')->where('id', $id)->update(['status' => 1]);
            DB::table('sys_sales_invoice_items')->where('si_id', $id)->update(['status' => 1]);
            DB::table('sys_sales_invoice_cf_charges')->where('si_id', $id)->update(['status' => 1]);
            DB::table('sys_chartofaccounts_transaction')->where('transaction_id', $id)->where('transaction_type', 'salesinvoice')->update(['status' => 1]);
            //DB::table('sys_crm_deals')->where('sales_invoice_id',$id)->update(['sales_invoice_id' => 0]);

            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    // create po cart from deal
    function deal_add_selected_deal_items_to_sales_invoice_cart(Request $request)
    {



        try {
            if (!isset($request->selected_item_id)) {
                Toastr::error('Operation Failed! No Items Selected', 'Failed');
                return redirect()->back();
            }
            DB::beginTransaction();
            $deal = SysCrmDeals::where('id', $request->deal_id)->first();
            $customer_reference = $deal->customername->name;
            $salesman_name = $deal->ownername->full_name ?? '';
            $deal_id = $deal->id;
            $deal_code = $request->deal_code;

            // dd($request->all());

            for ($i = 0; $i < count($request->item_id); $i++) {
                for ($j = 0; $j < count($request->selected_item_id); $j++) {
                    if ($request->selected_item_id[$j] == $request->roids[$i]) {
                        $data[] = [
                            'cart_id' => session('logged_session_data.cart_id'),
                            'quote_item_id' => $request->selected_item_id[$j],
                            'part_number' => $request->product_id[$i],
                            'part_number_txt' => $request->part_number[$i],
                            'description' => $request->description[$i],
                            'tax' => ($request->tax[$i] === '' ? '0.00' : $request->tax[$i]),
                            'qty' => $request->qty[$i],
                            'unitprice' => str_replace(',', '', $request->unitprice[$i]),
                            'value' => str_replace(',', '', $request->unitprice[$i]) * $request->qty[$i],
                            'discount' => str_replace(',', '', $request->discount[$i]),
                            'fright' => 0,
                            'customcharges' => 0,
                            'taxableamount' => (str_replace(',', '', $request->unitprice[$i]) * $request->qty[$i]) - str_replace(',', '', $request->discount[$i]),
                            'vatamount' => ((str_replace(',', '', $request->unitprice[$i]) * $request->qty[$i]) - str_replace(',', '', $request->discount[$i])) * $request->tax[$i] / 100,
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                            'created_at' => Carbon::now('+04:00'),
                            'refid' => $request->item_id[$i],
                            'deal_id' => $request->deal_id[$i],
                            'deal_qty' => $request->deal_qty[$i],
                        ];
                    }
                }
            }
            SysDealSalesInvoiceItemsCart::where('cart_id', session('logged_session_data.cart_id'))->delete();
            SysDealSalesInvoiceItems::where('cart_id', session('logged_session_data.cart_id'))->delete();
            SysDealSalesInvoiceItems::insert($data);

            // Persist selected expense/credit account/remarks values so they remain after refresh
            session([
                'deal_track_si_charge' => [
                    'selling_exp_account_id' => $request->selling_exp_account_id ?? [],
                    'selling_exp_account_amount' => $request->selling_exp_account_amount ?? [],
                    'selling_exp_credit_account' => $request->selling_exp_credit_account ?? [],
                    'selling_exp_remarks' => $request->selling_exp_remarks ?? [],
                    'jv_det_id' => $request->jv_det_id ?? [],
                    'jv_det_amount' => $request->jv_det_amount ?? [],
                    'jv_det_credit_account' => $request->jv_det_credit_account ?? [],
                    'jv_det_remarks' => $request->jv_det_remarks ?? [],
                ],
            ]);

            DB::commit();
            Toastr::success('Items added to cart successfully', 'Success');
            return redirect(
                'sales-invoice-deal-track-create'
                . '?customer_reference=' . urlencode($customer_reference)
                . '&salesman_name=' . urlencode($salesman_name)
                . '&deal_id=' . urlencode($deal_id)
                . '&deal_code=' . urlencode($deal_code)
            );



        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function salesinvoiceupdate_currency(Request $request)
    {
        try {
            if ($request->to_currency_id != $request->from_currency_id) {

                $to_currency = SysCurrencyRate::where('id', $request->to_currency_id)->value('to_currency');
                SysSalesInvoice::where('id', $request->cur_si_id)->update(['currency' => $to_currency]);
                $qt = SysSalesInvoiceItems::where('si_id', $request->cur_si_id)->get();
                $ca = SysChartofAccountsTransaction::where('transaction_id', $request->cur_si_id)->where('transaction_type', 'salesinvoice')->get();
                foreach ($qt as $t) {
                    $new_price = $t->unitprice * $request->to_currency_rate;
                    $new_discount = $t->discount * $request->to_currency_rate;

                    SysSalesInvoiceItems::where('id', $t->id)->update(
                        [
                            'unitprice' => $new_price,
                            'value' => $new_price * $t->qty,
                            'discount' => $new_discount,
                            'taxableamount' => ($new_price * $t->qty) - $new_discount + ($t->fright + $t->customcharges),
                            'vatamount' => (($new_price * $t->qty) - $new_discount + ($t->fright + $t->customcharges)) * $t->tax / 100,
                        ]
                    );
                }
                foreach ($ca as $t) {
                    $new_debit_amount = $t->debit_amount * $request->to_currency_rate;
                    $new_credit_amount = $t->credit_amount * $request->to_currency_rate;

                    SysChartofAccountsTransaction::where('id', $t->id)->update(
                        [
                            'debit_amount' => $new_debit_amount,
                            'credit_amount' => $new_credit_amount,
                        ]
                    );
                }
            }

            Toastr::success('Currency Updated Successfully. Please Update Sales Invoice', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function salesinvoiceupdate_adjustment(Request $request)
    {
        try {

            //db::beginTransaction();

            // SysReceiptAdjustments::where('bi_doc_number',$request->doc_number2)->where('account_id',$request->br_account_id)->delete();

            // $transaction = SysChartofAccountsTransaction::where('transaction_no',$request->doc_number2)->get();
            // if($transaction->where('account_id',$request->br_account_id)->count()==0){
            //     SysHelper::trn_chartof_accounts_transaction_with_main($request->br_account_id,$transaction[0]->transaction_id,$request->doc_number2,$transaction[0]->transaction_date,$request->transaction_type2,'0.00',$request->br_account_id_amount,'',1,0,"",1,0);
            // }

            // $amount = SysChartofAccountsTransaction::where(['transaction_no' => $request->doc_number2])->sum('credit_amount');
            // SysChartofAccountsTransaction::where(['transaction_no' => $request->doc_number2])
            // ->where(['is_main_account' => 1])->where(['credit_amount' => '0.00'])->update(['debit_amount' => $amount]);


            //SysReceiptAdjustments::where('account_id',$request->adj_cus_id)->where('created_from',1)->delete();

            $temp_data = [];
            $ret_data = [];
            for ($i = 0; $i < count($request->set_amt); $i++) {
                if ($request->set_amt[$i] != 0) {

                    $rec = SysReceipt::where('doc_number', $request->receiptno[$i])->first();

                    if (isset($rec)) {
                        $adjusted_amt = SysReceiptAdjustments::where('bi_doc_no', $request->adj_siv_no)->sum('bi_amount_adjusted');
                        if ($rec->mode == 1) {
                            $transaction_type = "cashreceipt";
                        } else {
                            $transaction_type = "bankreceipt";
                        }
                        $currency = $rec->currency;
                        $exe_type = "receipt";
                    } else {
                        $adjusted_amt = 0;
                        $rec = SysSalesReturn::where('doc_number', $request->receiptno[$i])->first();
                        if (isset($rec)) {
                            $currency = $rec->currency;
                            $transaction_type = "";
                            $exe_type = "return";
                        } else {
                            $currency = 1;
                            $transaction_type = "openingbalance";
                            $exe_type = "receipt";
                        }
                    }

                    if ($request->set_amt_act[$i] == $request->set_amt[$i]) {
                        $bi_balance_to_adjust = 0;
                        $bi_extra_amount = 0;
                    }
                    if ($request->set_amt_act[$i] > $request->set_amt[$i]) {
                        $bi_balance_to_adjust = 0;
                        $bi_extra_amount = $request->set_amt_act[$i] - $request->set_amt[$i];
                    }
                    if ($request->set_amt_act[$i] < $request->set_amt[$i]) {
                        $bi_balance_to_adjust = $request->set_amt[$i] - $request->set_amt_act[$i];
                        $bi_extra_amount = 0;
                    }

                    if ($exe_type == "receipt") {
                        $temp_data[] = [
                            'transaction_type' => $transaction_type,
                            'bi_cheque_amount' => $request->set_amt_act[$i],
                            'bi_amount_adjusted' => $request->set_amt[$i],
                            'bi_balance_to_adjust' => $request->adj_siv_amount_actual - ($request->set_amt[$i] + $adjusted_amt),
                            'bi_extra_amount' => $bi_extra_amount,
                            'bi_currency' => @$currency,
                            'bi_doc_number' => $request->receiptno[$i],
                            'bi_contains' => '',
                            'bi_doc_no' => $request->adj_siv_no,
                            'bi_lpo_no' => '',
                            'bi_doc_date' => $request->adj_siv_date,
                            'bi_total' => $request->adj_siv_amount,
                            'bi_paid' => $request->set_amt[$i],
                            'bi_balance' => $request->adj_siv_amount_actual - ($request->set_amt[$i] + $adjusted_amt),
                            'bi_extra_amount' => $bi_extra_amount,
                            'bi_amount' => $request->set_amt[$i],
                            'bi_narration' => "Adjusted from SI No: " . $request->adj_siv_no,
                            'account_id' => $request->adj_cus_id,
                            'status' => 1,
                            'created_from' => 1,
                            'created_by' => Auth::user()->id,
                            'created_at' => Carbon::now('+04:00'),
                            'company_id' => session('logged_session_data.company_id'),
                        ];
                    }
                    if ($exe_type == "return") {
                        $ret_data = [
                            'srn_no' => $request->receiptno[$i],
                            'dln_no' => @$rec->dn_doc_number,
                            'siv_no' => $request->adj_siv_no,
                            'lpo_number' => @$rec->lpo_number,
                            'doc_date' => date('Y-m-d', strtotime($request->adj_siv_date)),
                            'total_amount' => $request->set_amt_act[$i],
                            'paid_amount' => $request->set_amt[$i],
                            'balance_amount' => $request->set_amt_act[$i] - $request->set_amt[$i],
                            'narration' => 'Adjusted from SI No: ' . $request->adj_siv_no,
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                        ];
                    }
                }
            }
            if (count($temp_data) > 0) {
                SysReceiptAdjustments::insert($temp_data);
            }
            if (count($ret_data) > 0) {
                SysSalesReturnAdjestment::insert($ret_data);
            }
            // db::commit();


            Toastr::success('Adjustment Updated Successfully.', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            //db::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function salesinvoice_get_adjustment(Request $request)
    {
        try {
            $company_id = session('logged_session_data.company_id');

            $unadjusted = SysHelper::get_list_of_unadjusted([$request->customer], $company_id);
            $unadjusted_pdc = SysHelper::get_list_of_unadjusted_pdc([$request->customer], $company_id);

            return json_encode([
                'unadjusted' => $unadjusted,
                'unadjusted_pdc' => $unadjusted_pdc
            ]);

        } catch (\Exception $e) {
            return json_encode([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
    }
    function salesinvoice_add_adjustment_cart(Request $request)
    {
        try {

            $company_id = session('logged_session_data.company_id');
            DB::table('sys_sales_invoice_adjustment_temp')->where([
                'company_id' => $company_id,
                'user_id' => Auth::user()->id,
                'cart_id' => session('logged_session_data.cart_id')
            ])->delete();

            $adj_cus_id = $request->input('adj_cus_id');
            $adj_siv_amount_actual = $request->input('adj_siv_amount_actual');

            $receiptNos = $request->input('receiptno');
            $setAmts = $request->input('set_amt');
            $setAmtActs = $request->input('set_amt_act');

            foreach ($receiptNos as $index => $receiptNo) {
                $data[] = [
                    'cart_id' => session('logged_session_data.cart_id'),
                    'receiptno' => $receiptNos[$index],
                    'set_amt_act' => str_replace(',', '', $setAmtActs[$index]),
                    'set_amt' => $setAmts[$index],
                    'adj_cus_id' => $adj_cus_id,
                    'adj_siv_amount_actual' => $adj_siv_amount_actual,
                    'status' => 1,
                    'company_id' => $company_id,
                    'user_id' => Auth::user()->id,
                ];
            }

            if (count($data) > 0) {
                DB::table('sys_sales_invoice_adjustment_temp')->insert($data);
            }
            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            return json_encode([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function generateDeal($si_id)
    {
        try {
            DB::beginTransaction();
            $com = session('logged_session_data.company_id');
            $siv = SysSalesInvoice::where('id', $si_id)->first();
            $siv_item = SysSalesInvoiceItems::where('si_id', $si_id)->get();

            $cust = SysCustSuppl::select('sys_cust_suppl.*')
                ->join('sys_chartofaccounts as ca', 'ca.account_code', 'sys_cust_suppl.code')
                ->where('ca.id', $siv->customer)->first();
            //return $cust;

            $scd = new SysCrmDeals();
            $scd->code = SysHelper::get_new_code_lead('sys_crm_deals', 'DL', 'code', $com);
            $scd->date = date('Y-m-d', strtotime($siv->doc_date));
            $scd->deal_name = $siv->doc_number;
            $scd->cust_id = $cust->id;
            $scd->cust_name = $cust->first_name . ' ' . $cust->last_name;
            $scd->cust_no = $cust->contcat_number;
            $scd->cust_email = $cust->email;
            $scd->deal_value = $siv_item->sum('taxableamount');
            $scd->deal_currency = $siv->currency;
            $scd->source = 'Mail';
            $scd->source_o = '';
            $scd->tags = '';
            $scd->stage = 4;
            $scd->owner = $siv->sales_man;
            $scd->doc = '';
            $scd->isproject = 2;
            $scd->designation = $cust->designation;
            $scd->address = $cust->address;

            $scd->delivery_company = $cust->id;
            $scd->delivery_name = $siv->shipping_name;
            $scd->delivery_number = $cust->contcat_number;
            $scd->delivery_email = $cust->email;
            $scd->delivery_address = $siv->shipping_address;

            $scd->note = 'Deal Created from Sales Invoice No: ' . $siv->doc_number;
            $scd->status = 1;
            $scd->estimated_close_date = date('Y-m-d', strtotime($siv->lpo_date));
            $scd->created_by = Auth::user()->id;
            $scd->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $scd->company_id = $com;
            $scd->is_professional_service = 0;
            $scd->sales_invoice_id = $siv->id;
            $scd->save();
            $scd->toArray();

            $i = 1;
            foreach ($siv_item as $items) {
                DB::table('sys_crm_quote_items')->insert([
                    'user_id' => $scd->owner,
                    'deal_id' => $scd->id,
                    'company_id' => $scd->company_id,
                    'currency_id' => $siv->currency,
                    'customer_type' => $cust->account_type,
                    'quote_validity' => '3 Weeks',
                    'payment_terms' => $siv->payment_terms,
                    'delivery_date' => $siv->reference_date,
                    'payment_terms_txt' => $siv->payment_terms2,
                    'delivery_time' => '3 Weeks',
                    'product_id' => $items->part_number,
                    'qty' => $items->qty,
                    'price' => $items->unitprice,
                    'description' => $items->description,
                    'discount' => $items->discount,
                    'vat' => $items->tax,
                    'cost' => $items->cost,
                    'status' => $items->status,
                    'sort_id' => $i++,
                    'created_by' => Auth::user()->id,
                    'quote_id' => 1,
                ]);
            }

            SysSalesInvoice::where('id', $si_id)->update(['deal_id' => $scd->id]);

            DB::commit();
            return ["OK", $scd->id];
        } catch (\Throwable $th) {
            DB::rollBack();
            return ["ERROR", $th];
        }
    }
    public function generateDealTrack($deal_id)
    {
        try {
            DB::beginTransaction();
            $deal = SysCrmDeals::where('id', $deal_id)->first();
            $deal_items = SysCrmQuoteItems::where('deal_id', $deal_id)->first();
            $track = new SysCrmDealTrack();
            $track->deal_id = $deal_id;
            $track->delivery_date = date('Y-m-d', strtotime($deal->estimated_close_date));
            $track->payment_terms = $deal_items->payment_terms;

            if ($deal_items->payment_terms == 1) {
                $track->payment_mode = 1;
            } else {
                $track->payment_mode = 2;
            }

            //$track->payment_mode_sec = $request->payment_mode_sec;

            $track->lpo = "";
            $track->purchease_quote = "";
            $track->cheque_copy = "";
            $track->purchease_required = 0;
            $track->partial_delivery = 0;
            $track->technical = 0;
            $track->technical_detail = "";
            $track->remarks = "Deal Track created from sales Invoice";
            $track->reference_no = $deal->sales_invoice_id;
            $track->reference_date = $deal->date;

            $track->purchease_approval = 0;
            $track->invoice_approval = 1;
            $track->delivery_approval = 1;
            $track->receivables_approval = 1;
            //$track->start_date = $request->start_date;
            //$track->end_date = $request->end_date;

            $track->accounts = 1;
            $track->sales = 1;
            $track->purchease = 1;
            $track->invoice = 1;
            $track->delivery = 0;
            $track->receivables = 0;
            $track->tech = 0;
            $track->created_by = Auth::user()->id;
            $track->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $track->company_id = session('logged_session_data.company_id');
            $track->save();
            $track->toArray();

            DB::table('sys_crm_deal_track_approval_accounts')->insert(
                [
                    'deal_track_id' => $track->id,
                    'deal_id' => $deal_id,
                    'customer_status' => 1,
                    'credit_limit' => 1,
                    'payment_terms' => 1,
                    'pending_payment' => 1,
                    'other' => 1,
                    'remarks' => 'ADVANCE PAYMENT',
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                ]
            );

            DB::table('sys_crm_deal_track_approval_sales')->insert(
                [
                    'deal_track_id' => $track->id,
                    'deal_id' => $deal_id,
                    'margin' => 1,
                    'stock' => 1,
                    'purcease_quote' => 1,
                    'other' => 1,

                    'purchase_approval' => 2,
                    'invoice_approval' => 1,
                    'delivery_approval' => 1,
                    'receivables_approval' => 1,

                    'remarks' => $deal->deal_name,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                ]
            );

            DB::table('sys_crm_deal_track_approval_invoice')->insert(
                [
                    'deal_track_id' => $track->id,
                    'deal_id' => $deal_id,
                    'delivery_advice' => 1,
                    'validation' => 1,
                    'hold' => 1,
                    'print' => 1,
                    'remarks' => "",
                    'status' => 1,
                    'invoice_no' => $deal->deal_name,
                    'partial_invoice' => 0,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                ]
            );

            DB::commit();
            return "TRACK";

        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function generateDeliveryNote($si_id)
    {
        try {
            DB::beginTransaction();
            $siv = SysSalesInvoice::where('id', $si_id)->first();
            $siv_item = SysSalesInvoiceItems::where('si_id', $si_id)->get();
            $deal_track = SysCrmDealTrack::where('deal_id', $siv->deal_id)->first();


            $dn = new SysDeliveryNote();
            $dn->doc_number = SysHelper::get_new_code('sys_delivery_note', 'DN', 'doc_number');
            $dn->doc_date = date('Y-m-d', strtotime($siv->doc_date));
            $dn->ref_si_id = $siv->id;

            $dn->customer_id = $siv->customer;
            $dn->narration = $siv->narration;
            $dn->currency = $siv->currency;
            $dn->salesman = $siv->sales_man;
            $dn->lpo_no = $siv->lpo_number;
            $dn->lpo_date = date('Y-m-d', strtotime($siv->lpo_date));
            //$dn->issued_by = $request->issued_by;
            //$dn->received_by = $request->received_by;
            $dn->supplier_name = $siv->supplier_name;
            $dn->deal_id = $siv->deal_id;
            $dn->warehouse = "";
            $dn->driver = "";
            $dn->vehicleno = "";
            $dn->paymentterms = $siv->payment_terms;
            $dn->invoice_no = $siv->doc_number;
            $dn->invoice_date = date('Y-m-d', strtotime($siv->doc_date));
            $dn->status = 1;
            $dn->company_id = session('logged_session_data.company_id');
            $dn->created_by = Auth::user()->id;
            $dn->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $results = $dn->save();

            $dn->toArray();
            for ($i = 0; $i < count($siv_item); $i++) {
                $dnl = new SysDeliveryNoteItems();
                $dnl->dn_id = $dn->id;
                $dnl->ref_si_id = $dn->ref_si_id;
                $dnl->part_number = $siv_item[$i]->part_number;
                $dnl->description = $siv_item[$i]->description;
                $dnl->serial_no = $siv_item[$i]->serialno;
                $dnl->qty = $siv_item[$i]->qty;
                $dnl->tax = $siv_item[$i]->tax;
                $dnl->unitprice = $siv_item[$i]->unitprice;
                $dnl->value = $siv_item[$i]->value;
                $dnl->discount = $siv_item[$i]->discount;
                $dnl->taxableamount = $siv_item[$i]->taxableamount;
                $dnl->vatamount = $siv_item[$i]->vatamount;
                $dnl->status = 1;
                $dnl->created_by = Auth::user()->id;
                // if($request->btnSubmit==2){
                //     $dnl->is_deal_aditional = 1;
                // }
                $dnl->save();


                if (isset($request->row_id)) {
                    if (count($request->row_id)) {
                        $crm_quote_item = SysCrmQuoteItems::find($request->row_id[$i]);
                        if ($crm_quote_item != "") {
                            $crm_quote_item->dn_qty = $crm_quote_item->dn_qty + $dnl->qty;
                            $crm_quote_item->save();
                        }
                    }
                }

                $str_arr = explode(",", $siv_item[$i]->serialno);
                foreach ($str_arr as $srl) {
                    $values = array('dn_id' => $dn->id, 'part_number' => $siv_item[$i]->part_id, 'srl_no' => $srl);
                    DB::table('sys_delivery_note_items_srl')->insert($values);
                }




                //$key_item = SysPurchaseDlnLicenseKey::where('item_id',$request->part_id[$i])->where('dn_id',0)->where('cart_id',session('logged_session_data.cart_id'))->where('company_id',session('logged_session_data.company_id'))->get();
                //if(count($key_item)>0){
                //    SysPurchaseDlnLicenseKey::where('item_id',$request->part_id[$i])->where('dn_id',0)->where('cart_id',session('logged_session_data.cart_id'))->where('company_id',session('logged_session_data.company_id'))->update(['dn_id' => $dnl->id]);
                //}
                //if(count($key_item)>0){
                //    foreach($key_item as $k){
                //        SysPurchaseGrnLicenseKey::where('item_id',$request->part_id[$i])->where('license_key',$k->license_key)->where('company_id',session('logged_session_data.company_id'))->update(['status' => 2, 'dn_id' => $dnl->id]);
                //    }
                //}

                /*$total_tax_amount = array_sum($request->taxableamount);
                $total_cfc_amount1 = DB::select("SELECT SUM(cfc_amount) cfc_amount FROM sys_sales_invoice_cf_charges WHERE si_id=".$request->si_id."");
                if($total_cfc_amount1[0]->cfc_amount=="")
                {
                    $total_cfc_amount = 0;
                }
                else{ $total_cfc_amount = $total_cfc_amount1[0]->cfc_amount; }*/
                if ($siv_item[$i]->qty != 0) {
                    $discount = ($siv_item[$i]->discount === '' ? '0.00' : $siv_item[$i]->discount);
                    $istock = new SysItemStock();
                    $istock->dln_id = $dn->id;
                    $istock->account_id = $siv->customer;
                    $istock->partno = $siv_item[$i]->part_number;
                    $istock->qty_out = $siv_item[$i]->qty;
                    $istock->price_out = ($siv_item[$i]->value - $discount) / $siv_item[$i]->qty;
                    $istock->refno = $dn->invoice_no;
                    $istock->doc_number = $dn->doc_number;
                    $istock->doc_date = $dn->doc_date;
                    $istock->deal_id = $dn->deal_id;
                    $istock->slno = $siv_item[$i]->serialno;
                    $istock->status = 1;
                    $istock->created_by = Auth::user()->id;
                    $istock->company_id = session('logged_session_data.company_id');
                    $istock->currency_id = $siv->currency;
                    $istock->save();
                }
            }

            SysSalesInvoice::where('id', $si_id)->update(['dn_id' => $dn->id]);

            SysCrmDeals::where('id', $dn->deal_id)->update([
                'dln_id' => $dn->id,
            ]);

            DB::table('sys_crm_deal_track_approval_delivery')->insert(
                [
                    'deal_track_id' => $deal_track->id,
                    'deal_id' => $siv->deal_id,
                    'do_status' => 1,
                    'do_no' => $dn->doc_number,
                    'print_invoice_no' => $siv->doc_number,
                    'cheque_collection' => 1,
                    'cheque_collection_file' => "",
                    'delivery_status' => 1,
                    'deliver_by' => 3,
                    'driver' => "Mannan",
                    'remarks' => "",
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                    'contact_no' => "",
                    'id_no' => "",
                    'attach_file' => "",
                    'awb_no' => "",
                ]
            );
            DB::table('sys_crm_deal_track')->where('id', $deal_track->id)->update(['delivery' => 1]);

            $this->approveSalesInvoice($si_id, $siv->deal_id);

            DB::commit();
            return "DN";

        } catch (\Throwable $th) {

            DB::rollBack();
            return $th;
        }
    }

    public function approveSalesInvoice($si_id, $deal_id)
    {
        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');
        $sales_invoice = SysSalesInvoice::where('id', $si_id)->first();
        $deal_track = SysCrmDealTrack::where('deal_id', $deal_id)->first();
        $deal = SysCrmDeals::where('id', $deal_id)->first();
        $list_sales_invoice = SysSalesInvoice::where('deal_id', $deal->id)
            ->pluck('doc_number')
            ->implode(',');




        try {
            $status = 1;

            $check = DB::table('sys_crm_deal_track_approval_invoice')->select('id', 'remarks')->where(['deal_id' => $deal_id])->first();
            if (isset($check)) {
                DB::table('sys_crm_deal_track_approval_invoice')->where('id', $check->id)->update(
                    [
                        'delivery_advice' => 1,
                        'validation' => 1,
                        'hold' => 1,
                        'print' => 1,
                        'remarks' => "Sales Invoice Approved By Creating Delivery Note",
                        'status' => $status,
                        'invoice_no' => $list_sales_invoice,
                        'partial_invoice' => 0,
                        'partial_invoice_amount' => 0,
                        'updated_by' => Auth::user()->id,
                        'updated_at' => $trn_time,
                    ]
                );
            } else {
                DB::table('sys_crm_deal_track_approval_invoice')->insert(
                    [
                        'deal_track_id' => $deal_track->id,
                        'deal_id' => $deal_id,
                        'delivery_advice' => 1,
                        'validation' => 1,
                        'hold' => 1,
                        'print' => 1,
                        'remarks' => "Sales Invoice Approved By Creating Delivery Note",
                        'status' => $status,
                        'invoice_no' => $list_sales_invoice,
                        'partial_invoice' => 0,
                        'partial_invoice_amount' => 0,
                        'created_by' => Auth::user()->id,
                        'created_at' => $trn_time,
                        'created_date' => $trn_time,
                    ]
                );
            }

            DB::table('sys_crm_deals')->where('id', $deal_id)->update(['is_partial_invoice' => 0]);


            DB::table('sys_crm_deal_track')->where('deal_id', $deal_id)->update(['invoice' => $status, 'invoice_approval' => 1]);

            // if ($status == 2) {
            //     SysHelper::exe_web_push($request->owner_id, 'Deal Track Rejected', 'Deal ' . $request->deal_id, 'crm-deal-track/' . $request->deal_id . '/view');
            //     SysHelper::Erp_Notify_in($request->owner_id, 'Deal' . $request->deal_id . ' Rejected', $request->owner_id, 'http://erp.venushrms.com/crm-deal-track/' . $request->deal_id . '/view');
            //     SysHelper::Erp_Notify_track_reject($request->deal_id, $request->owner_name, $request->owner_email, "Invoice", $request->remarks);
            // }
            if ($status == 1) {

                $deals = SysCrmDeals::where('id', $deal_id)->first();
                $products = SysCrmQuoteItems::where('deal_id', $deal_id)->first();
                $deals_track = SysCrmDealTrack::where('deal_id', $deal_id)->first();
                if ($deals_track->start_date == null)
                    $start_date = date('Y-m-d h:i:s', time());
                else
                    $start_date = $deals_track->start_date;

                if ($deals_track->end_date == null)
                    $end_date = date('Y-m-d h:i:s', time());
                else
                    $end_date = $deals_track->end_date;

                $deals = SysCrmDeals::where('id', $deal_id)->first();
                $deals_item = SysCrmQuoteItems::wherein('product_id', [35657])->where('deal_id', $deal_id)->where('quote_id', $deals->quote_id)->first();

                $is_amc_item = SysCrmQuoteItems::wherein('product_id', [35657])->where('deal_id', $deal_id)->where('quote_id', $deals->quote_id)->count();

                if ($is_amc_item > 0) {
                    $invoice = SysHelper::get_amc_period($start_date, $end_date);
                    DB::table('sys_crm_amc_table')->insert(
                        [
                            'doc_number' => SysHelper::get_new_code('sys_crm_amc_table', 'AM', 'doc_number'),
                            'deal_id' => $deal_id,
                            'date' => Carbon::now('+04:00')->format('Y-m-d'),
                            'cust_name' => $deals->cust_id,
                            'contact_person' => $deals->cust_name,
                            'mobile_no' => $deals->cust_no,
                            'start_date' => $start_date,
                            'end_date' => $end_date,
                            'invoice' => !empty($deals_track->invoicing) ? $deals_track->invoicing : $invoice,
                            'amount' => $deals_item->price,
                            'sales_person' => $deals->owner,
                            'description' => $deals_item->description,
                            'status' => 1,
                            'is_auto' => 1,
                            'created_by' => Auth::user()->id,
                            'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                            'company_id' => session('logged_session_data.company_id'),
                        ]
                    );
                }

                //AMC Update;
                //SysHelper::set_amc_per_month($request->deal_id, $request->owner_id, 12);

                $user = DB::table('sm_staffs')->select('user_id')->where('role_id', 29)->get(); //Delivery
                if (count($user) > 0) {
                    foreach ($user as $u) {
                        SysHelper::exe_web_push($u->user_id, 'Deal Track Received', 'Deal ' . $deal->code, 'crm-deal-track-approval/' . $deal_track->id . '');
                        SysHelper::Erp_Notify_in($u->user_id, 'Deal Track Received', $u->user_id, 'http://erp.venushrms.com/crm-deal-track-approval/' . $deal_track->id . '');
                    }
                }
                SysHelper::Erp_Notify_in($deal->owner, 'Invoice Approved', $deal->owner, 'http://erp.venushrms.com/crm-deal-track-approval/' . $deal_track->id . '');


            }

            if ($status == 1) {
                SystemNotification::updateNotification('dealtrack', $deal_track->id, [
                    'role' => 'delivery',
                    'is_resolved' => false,
                    'is_account_rejected' => false,
                    'is_shown' => false,
                    'title' => 'Deal Track Delivery Approval Required',
                    'message' => 'Deal requires delivery approval',
                    'created_at' => Carbon::now('Asia/Dubai'),
                ]);
                Toastr::success('Approved successfully', 'Success');
            } else if ($status == 2) {
                Toastr::error('Rejected successfully', 'Rejected');
            } else {
                Toastr::warning('Updated successfully', 'Updated');
            }

            return redirect()->back();
        } catch (\Throwable $th) {

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function generateDeal_update($si_id)
    {
        try {
            DB::beginTransaction();
            $com = session('logged_session_data.company_id');
            $siv = SysSalesInvoice::where('id', $si_id)->first();
            $siv_item = SysSalesInvoiceItems::where('si_id', $si_id)->get();

            $cust = SysCustSuppl::select('sys_cust_suppl.*')
                ->join('sys_chartofaccounts as ca', 'ca.account_code', 'sys_cust_suppl.code')
                ->where('ca.id', $siv->customer)->first();
            //return $cust;

            $scd = SysCrmDeals::find($siv->deal_id);
            $scd->date = date('Y-m-d', strtotime($siv->doc_date));
            $scd->deal_name = $siv->doc_number;
            $scd->cust_id = $cust->id;
            $scd->cust_name = $cust->first_name . ' ' . $cust->last_name;
            $scd->cust_no = $cust->contcat_number;
            $scd->cust_email = $cust->email;
            $scd->deal_value = $siv_item->sum('taxableamount');
            $scd->deal_currency = $siv->currency;
            $scd->source = 'Mail';
            $scd->source_o = '';
            $scd->tags = '';
            $scd->stage = 4;
            $scd->owner = $siv->sales_man;
            $scd->doc = '';
            $scd->isproject = 2;
            $scd->designation = $cust->designation;
            $scd->address = $cust->address;

            $scd->delivery_company = $cust->name;
            $scd->delivery_name = $siv->shipping_name;
            $scd->delivery_number = $cust->contcat_number;
            $scd->delivery_email = $cust->email;
            $scd->delivery_address = $siv->shipping_address;

            $scd->note = 'Deal Created from Sales Invoice No: ' . $siv->doc_number;
            $scd->status = 1;
            $scd->estimated_close_date = date('Y-m-d', strtotime($siv->lpo_date));
            $scd->created_by = Auth::user()->id;
            $scd->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $scd->company_id = $com;
            $scd->is_professional_service = 0;
            $scd->sales_invoice_id = $siv->id;
            $scd->save();
            $scd->toArray();

            $i = 1;
            DB::table('sys_crm_quote_items')->where('deal_id', $siv->deal_id)->delete();
            foreach ($siv_item as $items) {
                DB::table('sys_crm_quote_items')->insert([
                    'user_id' => $scd->owner,
                    'deal_id' => $scd->id,
                    'company_id' => $scd->company_id,
                    'currency_id' => $siv->currency,
                    'customer_type' => $cust->account_type,
                    'quote_validity' => '3 Weeks',
                    'payment_terms' => $siv->payment_terms,
                    'delivery_date' => $siv->reference_date,
                    'payment_terms_txt' => $siv->payment_terms2,
                    'delivery_time' => '3 Weeks',
                    'product_id' => $items->part_number,
                    'qty' => $items->qty,
                    'price' => $items->unitprice,
                    'description' => $items->description,
                    'discount' => $items->discount,
                    'vat' => $items->tax,
                    'cost' => $items->unitprice,
                    'status' => $items->status,
                    'sort_id' => $i++,
                    'created_by' => Auth::user()->id,
                    'quote_id' => 1,
                ]);
            }

            SysSalesInvoice::where('id', $si_id)->update(['deal_id' => $scd->id]);

            DB::commit();
            return ["OK", $scd->id];
        } catch (\Throwable $th) {
            DB::rollBack();
            return ["ERROR", $th];
        }
    }
    public function generateDeliveryNote_update($si_id)
    {
        try {
            DB::beginTransaction();
            $siv = SysSalesInvoice::where('id', $si_id)->first();
            $siv_item = SysSalesInvoiceItems::where('si_id', $si_id)->get();
            $deal_track = SysCrmDealTrack::where('deal_id', $siv->deal_id)->first();


            $dn = SysDeliveryNote::find($siv->dn_id);
            $dn->doc_date = date('Y-m-d', strtotime($siv->doc_date));
            $dn->ref_si_id = $siv->id;

            $dn->customer_id = $siv->customer;
            $dn->narration = $siv->narration;
            $dn->currency = $siv->currency;
            $dn->salesman = $siv->sales_man;
            $dn->lpo_no = $siv->lpo_number;
            $dn->lpo_date = date('Y-m-d', strtotime($siv->lpo_date));
            //$dn->issued_by = $request->issued_by;
            //$dn->received_by = $request->received_by;
            $dn->supplier_name = $siv->supplier_name;
            $dn->deal_id = $siv->deal_id;
            $dn->warehouse = "";
            $dn->driver = "";
            $dn->vehicleno = "";
            $dn->paymentterms = $siv->payment_terms;
            $dn->invoice_no = $siv->doc_number;
            $dn->invoice_date = date('Y-m-d', strtotime($siv->doc_date));
            $dn->status = 1;
            $dn->company_id = session('logged_session_data.company_id');
            $dn->created_by = Auth::user()->id;
            $dn->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $results = $dn->save();

            $dn->toArray();

            SysDeliveryNoteItems::where('dn_id', $dn->id)->delete();
            SysItemStock::where('dln_id', $dn->id)->delete();
            DB::table('sys_delivery_note_items_srl')->where('dn_id', $dn->id)->delete();

            for ($i = 0; $i < count($siv_item); $i++) {
                $dnl = new SysDeliveryNoteItems();
                $dnl->dn_id = $dn->id;
                $dnl->ref_si_id = $dn->ref_si_id;
                $dnl->part_number = $siv_item[$i]->part_number;
                $dnl->description = $siv_item[$i]->description;
                $dnl->serial_no = $siv_item[$i]->serialno;
                $dnl->qty = $siv_item[$i]->qty;
                $dnl->tax = $siv_item[$i]->tax;
                $dnl->unitprice = $siv_item[$i]->unitprice;
                $dnl->value = $siv_item[$i]->value;
                $dnl->discount = $siv_item[$i]->discount;
                $dnl->taxableamount = $siv_item[$i]->taxableamount;
                $dnl->vatamount = $siv_item[$i]->vatamount;
                $dnl->status = 1;
                $dnl->created_by = Auth::user()->id;
                // if($request->btnSubmit==2){
                //     $dnl->is_deal_aditional = 1;
                // }
                $dnl->save();


                if (isset($request->row_id)) {
                    if (count($request->row_id)) {
                        $crm_quote_item = SysCrmQuoteItems::find($request->row_id[$i]);
                        if ($crm_quote_item != "") {
                            $crm_quote_item->dn_qty = $crm_quote_item->dn_qty + $dnl->qty;
                            $crm_quote_item->save();
                        }
                    }
                }

                $str_arr = explode(",", $siv_item[$i]->serialno);
                foreach ($str_arr as $srl) {
                    $values = array('dn_id' => $dn->id, 'part_number' => $siv_item[$i]->part_id, 'srl_no' => $srl);
                    DB::table('sys_delivery_note_items_srl')->insert($values);
                }


                if ($siv_item[$i]->qty != 0) {
                    $discount = ($siv_item[$i]->discount === '' ? '0.00' : $siv_item[$i]->discount);
                    $istock = new SysItemStock();
                    $istock->dln_id = $dn->id;
                    $istock->account_id = $siv->customer;
                    $istock->partno = $siv_item[$i]->part_number;
                    $istock->qty_out = $siv_item[$i]->qty;
                    $istock->price_out = ($siv_item[$i]->value - $discount) / $siv_item[$i]->qty;
                    $istock->refno = $dn->invoice_no;
                    $istock->doc_number = $dn->doc_number;
                    $istock->doc_date = $dn->doc_date;
                    $istock->deal_id = $dn->deal_id;
                    $istock->slno = $siv_item[$i]->serialno;
                    $istock->status = 1;
                    $istock->created_by = Auth::user()->id;
                    $istock->company_id = session('logged_session_data.company_id');
                    $istock->currency_id = $siv->currency;
                    $istock->save();
                }
            }

            SysSalesInvoice::where('id', $si_id)->update(['dn_id' => $dn->id]);

            SysCrmDeals::where('id', $dn->deal_id)->update([
                'dln_id' => $dn->id,
            ]);



            DB::commit();
            return "DN";

        } catch (\Throwable $th) {
            DB::rollBack();
            //return $th;
        }
    }


    function add_sales_invoice_attachment(Request $request)
    {
        try {
            $selected_file = "";
            if ($request->hasFile('att_file') && $request->file('att_file')->isValid()) {
                // Store the file (e.g., in the 'uploads' folder)
                if ($request->file('att_file') != "") {
                    $file = $request->file('att_file');
                    $selected_file = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/product_upload/', $selected_file);
                    $selected_file = 'public/uploads/product_upload/' . $selected_file;
                }
            }


            $data[] = [
                'cart_id' => session('logged_session_data.cart_id'),
                'siv_id' => $request->siv_id,
                'doc_file' => $selected_file,
                'doc_date' => date('Y-m-d', strtotime($request->att_date)),
                'doc_name' => $request->doc_name,
                'status' => 1,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now('+04:00'),
                'company_id' => session('logged_session_data.company_id'),
            ];

            DB::table('sys_sales_invoice_att')->insert($data);

            if ($request->siv_id == 0) {
                $ret = DB::table('sys_sales_invoice_att')->where('siv_id', $request->siv_id)->where('cart_id', session('logged_session_data.cart_id'))->where('company_id', session('logged_session_data.company_id'))->get();
            } else {
                $ret = DB::table('sys_sales_invoice_att')->where('siv_id', $request->siv_id)->where('company_id', session('logged_session_data.company_id'))->get();
            }
            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            //$ret = 'ERROR';
            $ret = $e;
            return json_encode(array('data' => $ret));
        }
    }
    function view_sales_invoice_attachment(Request $request)
    {
        try {
            if ($request->siv_id == 0) {
                $ret = DB::table('sys_sales_invoice_att')->where('siv_id', $request->siv_id)->where('cart_id', session('logged_session_data.cart_id'))->where('company_id', session('logged_session_data.company_id'))->get();
            } else {
                $ret = DB::table('sys_sales_invoice_att')->where('siv_id', $request->siv_id)->where('company_id', session('logged_session_data.company_id'))->get();
            }
            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }
    function view_sales_invoice_attachment_by_invoice_no(Request $request)
    {
        try {
            $siv_id = SysSalesInvoice::where('doc_number', $request->si_no)->value('id');
            $ret = DB::table('sys_sales_invoice_att')->where('siv_id', @$siv_id)->where('company_id', session('logged_session_data.company_id'))->get();
            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }
    function delete_sales_invoice_attachment(Request $request)
    {
        try {
            DB::table('sys_sales_invoice_att')->where('id', $request->id)->delete();
            if ($request->siv_id == 0) {
                $ret = DB::table('sys_sales_invoice_att')->where('siv_id', $request->siv_id)->where('cart_id', session('logged_session_data.cart_id'))->where('company_id', session('logged_session_data.company_id'))->get();
            } else {
                $ret = DB::table('sys_sales_invoice_att')->where('siv_id', $request->siv_id)->where('company_id', session('logged_session_data.company_id'))->get();
            }

            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

}
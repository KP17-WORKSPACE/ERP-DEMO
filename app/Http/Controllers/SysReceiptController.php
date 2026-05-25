<?php

namespace App\Http\Controllers;

use App\SysChartofAccounts;
use App\SysAccountType;
use App\ApiBaseMethod;
use App\SmStaff;
use App\SysBankReceipt;
use App\SysCashReceiptList;
use App\SysCustSuppl;
use App\SysAccountGroup;
use App\SysChartofAccountsDetails;
use App\SysChartofAccountsTransaction;
use App\SysCompany;
use App\SysCrmDeals;
use App\SysCurrencySettings;
use App\SysHelper;
use App\SysLedgerEntries;
use App\SysLedgerEntriesTemp;
use App\SysPurchaseInvoice;
use App\SysReceipt;
use App\SysReceiptAdjustments;
use App\SysReceiptAdjustmentsTemp;
use App\SysReceiptMode;
use App\SysSalesInvoice;
use App\SysSalesReturn;
use App\SysTransactions;
use App\SysReceiptAttachment;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;
use App\SysCrmDealTrack;
use App\SysCrmQuoteItems;
use App\User;
use App\SystemNotification;
use App\SysCrmDealTrackApprovalReceivables;
use App\SysPayment;

class SysReceiptController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function receiptList(Request $request, $id = null)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $ctrl_doc_number = "";
            $ctrl_receipt_mode = "";
            $ctrl_receipt_through = "";
            $ctrl_account_name = "";
            $ctrl_amount = "";
            $ctrl_doc_date = "";
            $ctrl_receipt_date = "";
            $ctrl_cheque_date = "";
            $ctrl_cheque_number = "";
            $ctrl_cheque_bank_name = "";
            $ctrl_deal_id = "";
            $ctrl_created_by = "";

            $receiptmode_cash = SysHelper::get_cash_account();
            $receiptmode_bank = SysHelper::get_bank_account();

            $data1 = $receiptmode_cash->pluck('id');
            $data2 = $receiptmode_bank->pluck('id');
            $data3 = array_merge($data1->toArray(), $data2->toArray());
            $accounts = SysHelper::get_customer_list($company_id);
            $receipt_mode_list = array_merge($receiptmode_cash->toArray(), $receiptmode_bank->toArray());

            $staff_list = SysHelper::get_staff_list();

            $query = SysReceipt::select('sys_receipt.id', 'sys_receipt.doc_number', 'sys_receipt.mode', 'sys_receipt.currency', 'sys_receipt.receipt_mode', 'sys_receipt.receipt_through', 'a.account_name', 'c.debit_amount', 'c.credit_amount', 'sys_receipt.doc_date', 'sys_receipt.receipt_date', 'sys_receipt.cheque_date', 'sys_receipt.cheque_number', 'sys_receipt.cheque_bank_name', 'u.full_name', 'sys_receipt.narration', 'sys_receipt.status', 'sys_receipt.deal_id')->selectRaw('1 as type')
                ->leftjoin('sys_chartofaccounts_transaction as c', 'c.transaction_no', 'sys_receipt.doc_number')
                ->leftjoin('sys_chartofaccounts as a', 'a.id', 'c.account_id')
                ->leftjoin('users as u', 'u.id', 'sys_receipt.created_by');

            $doc = SysChartofAccountsTransaction::wherein('transaction_type', ['bankreceipt', 'cashreceipt'])->wherein('company_id', $company_id)->wherenotin('account_id', $data3)->pluck('transaction_no');
            $query2 = SysReceipt::select('sys_receipt.id', 'sys_receipt.doc_number', 'sys_receipt.mode', 'sys_receipt.receipt_mode', 'sys_receipt.receipt_through', 'sys_receipt.doc_date', 'sys_receipt.receipt_date', 'sys_receipt.cheque_date', 'sys_receipt.cheque_number', 'sys_receipt.cheque_bank_name', 'u.full_name', 'sys_receipt.narration', 'sys_receipt.status', 'sys_receipt.deal_id')->selectRaw('2 as type')
                ->leftjoin('users as u', 'u.id', 'sys_receipt.created_by')
                ->wherenotin('doc_number', $doc)->wherein('sys_receipt.company_id', $company_id);

            if (SysHelper::get_pagination_post($request)) {

                if ($request->doc_number != "") {
                    $query->where('sys_receipt.doc_number', $request->doc_number);
                    $query2->where('sys_receipt.doc_number', $request->doc_number);
                    $ctrl_doc_number = $request->doc_number;
                }
                if ($request->receipt_mode != "") {
                    $query->where('sys_receipt.receipt_mode', $request->receipt_mode);
                    $ctrl_receipt_mode = $request->receipt_mode;
                }
                if ($request->receipt_through != "") {
                    if ($request->receipt_through == 0) {
                        $query->where('sys_receipt.mode', 1);
                    } else {
                        $query->where('sys_receipt.receipt_through', $request->receipt_through);
                    }
                    $ctrl_receipt_through = $request->receipt_through;
                }
                if ($request->account_name != "") {
                    $query->where('c.account_id', $request->account_name);
                    $ctrl_account_name = $request->account_name;
                }
                if ($request->amount != "") {
                    $query->where('c.credit_amount', $request->amount);
                    $ctrl_amount = $request->amount;
                }
                if ($request->doc_date != "") {
                    try {
                        $docDate = Carbon::createFromFormat('d/m/Y', $request->doc_date)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $docDate = $request->doc_date;
                    }
                    $query->where('sys_receipt.doc_date', $docDate);
                    $ctrl_doc_date = $request->doc_date;
                }
                if ($request->receipt_date != "") {
                    try {
                        $receiptDate = Carbon::createFromFormat('d/m/Y', $request->receipt_date)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $receiptDate = $request->receipt_date;
                    }
                    $query->where('sys_receipt.receipt_date', $receiptDate);
                    $ctrl_receipt_date = $request->receipt_date;
                }
                if ($request->cheque_date != "") {
                    try {
                        $chequeDate = Carbon::createFromFormat('d/m/Y', $request->cheque_date)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $chequeDate = $request->cheque_date;
                    }
                    $query->where('sys_receipt.cheque_date', $chequeDate);
                    $ctrl_cheque_date = $request->cheque_date;
                }
                if ($request->cheque_number != "") {
                    $query->where('sys_receipt.cheque_number', $request->cheque_number);
                    $ctrl_cheque_number = $request->cheque_number;
                }
                if ($request->cheque_bank_name != "") {
                    $query->where('sys_receipt.cheque_bank_name', $request->cheque_bank_name);
                    $ctrl_cheque_bank_name = $request->cheque_bank_name;
                }
                if ($request->deal_id != "") {
                    $dealid = SysHelper::get_dealid_from_code($request->deal_id);
                    $query->whereRaw("find_in_set($dealid,sys_receipt.deal_id)");
                    $ctrl_deal_id = $request->deal_id;
                }
                if ($request->created_by != "") {
                    $query->where('sys_receipt.created_by', $request->created_by);


                    $ctrl_created_by = $request->created_by;
                }
            }

            $query->wherenotin('c.account_id', $data3);
            $query->wherein('sys_receipt.company_id', $company_id);
            $query->orderby('sys_receipt.id', 'desc');
            $receipt = $query->orderby('sys_receipt.doc_number', 'desc')->orderby('sys_receipt.receipt_date', 'desc')->get();


            $receipt2 = $query2->orderby('sys_receipt.doc_number', 'desc')->orderby('sys_receipt.receipt_date', 'desc')->get();
            $receipt = $receipt->merge($receipt2);

            if ($id == null) {
                if ($receipt->first())
                    $id = $receipt->first()->id;
            }


            $active_id = $id;
            $data = $this->get_receipt_pdf_data($id);

            return view('backEnd.receipt.receiptlist', compact('receipt', 'accounts', 'receipt_mode_list', 'staff_list', 'ctrl_doc_number', 'ctrl_receipt_mode', 'ctrl_receipt_through', 'ctrl_account_name', 'ctrl_amount', 'ctrl_doc_date', 'ctrl_receipt_date', 'ctrl_cheque_date', 'ctrl_cheque_number', 'ctrl_cheque_bank_name', 'ctrl_deal_id', 'ctrl_created_by', 'data'));

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
            $searchquery = $request->get('query');

            $receiptmode_cash = SysHelper::get_cash_account();
            $receiptmode_bank = SysHelper::get_bank_account();

            $data1 = $receiptmode_cash->pluck('id');
            $data2 = $receiptmode_bank->pluck('id');
            $data3 = array_merge($data1->toArray(), $data2->toArray());
            $accounts = SysHelper::get_customer_list($company_id);
            $receipt_mode_list = array_merge($receiptmode_cash->toArray(), $receiptmode_bank->toArray());

            $staff_list = SysHelper::get_staff_list();

            $isAccountCode = SysHelper::getCompanyCodeSettings()['is_account_code'] ?? false;
            $firstAccountNameSql = $isAccountCode
                ? "COALESCE((SELECT CONCAT(a2.account_name, ' (', a2.account_code, ')') FROM sys_chartofaccounts_transaction t2 LEFT JOIN sys_chartofaccounts a2 ON a2.id = t2.account_id WHERE t2.transaction_id = sys_receipt.id AND t2.transaction_type IN ('cashreceipt','bankreceipt') AND t2.is_main_account = 0 LIMIT 1),'') as first_account_name"
                : "COALESCE((SELECT a2.account_name FROM sys_chartofaccounts_transaction t2 LEFT JOIN sys_chartofaccounts a2 ON a2.id = t2.account_id WHERE t2.transaction_id = sys_receipt.id AND t2.transaction_type IN ('cashreceipt','bankreceipt') AND t2.is_main_account = 0 LIMIT 1),'') as first_account_name";

            $query = SysReceipt::select('sys_receipt.id', 'sys_receipt.doc_number', 'sys_receipt.doc_date', 'sys_receipt.mode', 'sys_currency.code as currency_code', 'sys_receipt.receipt_through', 'a.account_code', 'a.account_name', 'c.debit_amount', 'c.credit_amount')
                ->selectRaw($firstAccountNameSql)
                ->selectRaw('1 as type')
                ->leftjoin('sys_chartofaccounts_transaction as c', 'c.transaction_no', 'sys_receipt.doc_number')
                ->leftjoin('sys_chartofaccounts as a', 'a.id', 'sys_receipt.receipt_mode')
                ->leftjoin('users as u', 'u.id', 'sys_receipt.created_by')
                ->join('sys_currency', 'sys_currency.id', '=', 'sys_receipt.currency');

            $doc = SysChartofAccountsTransaction::wherein('transaction_type', ['bankreceipt', 'cashreceipt'])->wherein('company_id', $company_id)->wherenotin('account_id', $data3)->pluck('transaction_no');
            $query2 = SysReceipt::select('sys_receipt.id', 'sys_receipt.doc_number', 'sys_receipt.mode', 'sys_receipt.receipt_through', 'sys_receipt.doc_date')
                ->selectRaw($firstAccountNameSql)
                ->selectRaw('2 as type')
                ->leftjoin('users as u', 'u.id', 'sys_receipt.created_by')
                ->wherenotin('doc_number', $doc)->wherein('sys_receipt.company_id', $company_id);

            $query->wherenotin('c.account_id', $data3);
            $query->wherein('sys_receipt.company_id', $company_id)
                ->where('sys_receipt.doc_number', 'LIKE', "%{$searchquery}%");
            $query->orderby('sys_receipt.id', 'desc');
            $receipt = $query->orderby('sys_receipt.doc_number', 'desc')->orderby('sys_receipt.receipt_date', 'desc')->limit(20)->get();


            $receipt2 = $query2->orderby('sys_receipt.doc_number', 'desc')->orderby('sys_receipt.receipt_date', 'desc')->limit(20)->get();
            $invoices = $receipt->merge($receipt2);


            return response()->json($invoices);
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function getDetails($id)
    {
        $data = $this->get_receipt_pdf_data($id);
        if (count($data) > 0) {
            return view('backEnd.receipt.r_details', $data);
        } else {
            return "error!!";
        }
    }

    public function getDetailsPDF($id)
    {
        $data = $this->get_receipt_pdf_data($id);
        if (count($data) > 0) {
            return view('backEnd.receipt.r_details-pdf', $data);
        } else {
            return "error!!";
        }
    }

    public function get_receipt_pdf_data($id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $receipt = SysReceipt::find($id);
            $receiptList = SysChartofAccountsTransaction::where('transaction_id', $id)->wherein('transaction_type', ['cashreceipt', 'bankreceipt'])->where('is_main_account', 0)->get();
            $company = SysCompany::find($receipt->company_id);
            $print = date('d/m/Y h:i A', strtotime(Carbon::now('+04:00')));

            $data = [
                'company' => $company,
                'receipt' => $receipt,
                'receipt_item' => $receiptList,
                'print' => $print,
            ];
            return $data;
        } catch (\Throwable $th) {
            return [];
        }
    }

    public function receiptAdd(Request $request, $page_id = null)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $accounts = SysHelper::get_customer_list($company_id);
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $receiptmode_cash = SysHelper::get_cash_account();
            $receiptmode_bank = SysHelper::get_bank_account();
            //return $receiptmode_bank;

            $currency = SysCurrencySettings::select('id', 'code')->get();
            $deal_id = 0;
            $mode_id = 0;
            $account_id = "";
            $amount = "";

            $data1 = $receiptmode_cash->pluck('id');
            $data2 = $receiptmode_bank->pluck('id');
            $data3 = array_merge($data1->toArray(), $data2->toArray());
            $accounts = SysHelper::get_customer_list($company_id);
            $receipt_mode_list = array_merge($receiptmode_cash->toArray(), $receiptmode_bank->toArray());

            $staff_list = SysHelper::get_staff_list();

            $query = SysReceipt::select('sys_receipt.id', 'sys_receipt.doc_number', 'sys_receipt.mode', 'sys_receipt.receipt_mode', 'sys_receipt.receipt_through', 'a.account_name', 'c.debit_amount', 'c.credit_amount', 'sys_receipt.doc_date', 'sys_receipt.receipt_date', 'sys_receipt.cheque_date', 'sys_receipt.cheque_number', 'sys_receipt.cheque_bank_name', 'u.full_name', 'sys_receipt.narration', 'sys_receipt.status', 'sys_receipt.deal_id')->selectRaw('1 as type')
                ->leftjoin('sys_chartofaccounts_transaction as c', 'c.transaction_no', 'sys_receipt.doc_number')
                ->leftjoin('sys_chartofaccounts as a', 'a.id', 'c.account_id')
                ->leftjoin('users as u', 'u.id', 'sys_receipt.created_by');

            $doc = SysChartofAccountsTransaction::wherein('transaction_type', ['bankreceipt', 'cashreceipt'])->wherein('company_id', $company_id)->wherenotin('account_id', $data3)->pluck('transaction_no');
            $query2 = SysReceipt::select('sys_receipt.id', 'sys_receipt.doc_number', 'sys_receipt.mode', 'sys_receipt.receipt_mode', 'sys_receipt.receipt_through', 'sys_receipt.doc_date', 'sys_receipt.receipt_date', 'sys_receipt.cheque_date', 'sys_receipt.cheque_number', 'sys_receipt.cheque_bank_name', 'u.full_name', 'sys_receipt.narration', 'sys_receipt.status', 'sys_receipt.deal_id')->selectRaw('2 as type')
                ->leftjoin('users as u', 'u.id', 'sys_receipt.created_by')
                ->wherenotin('doc_number', $doc)->wherein('sys_receipt.company_id', $company_id);
            $query->wherenotin('c.account_id', $data3);
            $query->wherein('sys_receipt.company_id', $company_id);
            $query->orderby('sys_receipt.id', 'desc');
            $receipt = $query->orderby('sys_receipt.doc_number', 'desc')->orderby('sys_receipt.receipt_date', 'desc')->get();


            $receipt2 = $query2->orderby('sys_receipt.doc_number', 'desc')->orderby('sys_receipt.receipt_date', 'desc')->get();
            $receipt = $receipt->merge($receipt2);
            $page = "normal";



            return view('backEnd.receipt.receiptadd', compact('accounts', 'receipt_mode_list', 'staff_list', 'receiptmode_cash', 'receiptmode_bank', 'currency', 'deal_id', 'mode_id', 'page_id', 'company', 'account_id', 'amount', 'receipt', 'page'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function receiptAddDeal($id, $mode_id, $page_id = null)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $accounts = SysHelper::get_customer_list($company_id);
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $receiptmode_cash = SysHelper::get_cash_account();
            $receiptmode_bank = SysHelper::get_bank_account();
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $deal_id = $id;

            $deal = SysCrmDeals::select('id', 'code', 'deal_value', 'deal_currency', 'cust_id', 'quote_id', 'deal_discount', 'deal_discount_vat')->where('id', $id)->first();
            $quoteitems = SysCrmQuoteItems::select('price', 'qty', 'discount', 'vat')->where('deal_id', $id)->where('quote_id', @$deal->quote_id)->get();
            $gross_quote_total = 0;
            if (count($quoteitems) > 0) {
                foreach ($quoteitems as $Item) {
                    $value = $Item->price * $Item->qty;
                    $taxableamount = $value - $Item->discount;
                    $vatamount = $taxableamount * $Item->vat / 100;
                    $gross_quote_total += $vatamount + $taxableamount;
                }
            }

            // Match deal-track receivable logic: net quote total minus deal-level discount, then subtract receipts already linked to this deal.
            $deal_discount_sum = (float) ($deal->deal_discount ?? 0) + (float) (! empty($deal->deal_discount_vat) ? $deal->deal_discount_vat : 0);
            $total_due = max(0, $gross_quote_total - $deal_discount_sum);

            $r_deal_id = (int) $id;
            $check_receipt_rows = DB::table('sys_receipt as r')
                ->select('r.doc_number', 'r.id', DB::raw('sum(c.debit_amount) as amount'))
                ->join('sys_chartofaccounts_transaction as c', 'c.transaction_no', '=', 'r.doc_number')
                ->whereRaw('FIND_IN_SET(?, r.deal_id) > 0', [$r_deal_id])
                ->groupBy('r.doc_number', 'r.id')
                ->get();
            $total_received = (float) $check_receipt_rows->sum('amount');

            $remaining = max(0, $total_due - $total_received);
            $amount = $remaining > 0 ? SysHelper::com_curr_format($remaining, 2, '.', '') : '0';

            $account_set = SysCustSuppl::select('c.id', 'c.account_code', 'c.account_name')->join('sys_chartofaccounts as c', 'c.account_code', 'sys_cust_suppl.code')->where('sys_cust_suppl.id', $deal->cust_id)->first();
            if (isset($account_set)) {
                $account_id = $account_set->id;
                $account_code = $account_set->account_code;
                $account_name = $account_set->account_name;
                //$amount=SysHelper::com_curr_format($deal->deal_value,2,'.','');
            } else {
                $account_id = 0;
                $account_code = "";
                $account_name = "";
                //$amount=0;
            }

            $receipt_mode_list = array_merge($receiptmode_cash->toArray(), $receiptmode_bank->toArray());
            $staff_list = SysHelper::get_staff_list();

            $data1 = $receiptmode_cash->pluck('id');
            $data2 = $receiptmode_bank->pluck('id');
            $data3 = array_merge($data1->toArray(), $data2->toArray());
            $accounts = SysHelper::get_customer_list($company_id);
            $receipt_mode_list = array_merge($receiptmode_cash->toArray(), $receiptmode_bank->toArray());

            $staff_list = SysHelper::get_staff_list();

            $query = SysReceipt::select('sys_receipt.id', 'sys_receipt.doc_number', 'sys_receipt.mode', 'sys_receipt.receipt_mode', 'sys_receipt.receipt_through', 'a.account_name', 'c.debit_amount', 'c.credit_amount', 'sys_receipt.doc_date', 'sys_receipt.receipt_date', 'sys_receipt.cheque_date', 'sys_receipt.cheque_number', 'sys_receipt.cheque_bank_name', 'u.full_name', 'sys_receipt.narration', 'sys_receipt.status', 'sys_receipt.deal_id')->selectRaw('1 as type')
                ->leftjoin('sys_chartofaccounts_transaction as c', 'c.transaction_no', 'sys_receipt.doc_number')
                ->leftjoin('sys_chartofaccounts as a', 'a.id', 'c.account_id')
                ->leftjoin('users as u', 'u.id', 'sys_receipt.created_by');

            $doc = SysChartofAccountsTransaction::wherein('transaction_type', ['bankreceipt', 'cashreceipt'])->wherein('company_id', $company_id)->wherenotin('account_id', $data3)->pluck('transaction_no');
            $query2 = SysReceipt::select('sys_receipt.id', 'sys_receipt.doc_number', 'sys_receipt.mode', 'sys_receipt.receipt_mode', 'sys_receipt.receipt_through', 'sys_receipt.doc_date', 'sys_receipt.receipt_date', 'sys_receipt.cheque_date', 'sys_receipt.cheque_number', 'sys_receipt.cheque_bank_name', 'u.full_name', 'sys_receipt.narration', 'sys_receipt.status', 'sys_receipt.deal_id')->selectRaw('2 as type')
                ->leftjoin('users as u', 'u.id', 'sys_receipt.created_by')
                ->wherenotin('doc_number', $doc)->wherein('sys_receipt.company_id', $company_id);
            $query->wherenotin('c.account_id', $data3);
            $query->wherein('sys_receipt.company_id', $company_id);
            $query->orderby('sys_receipt.id', 'desc');
            $receipt = $query->orderby('sys_receipt.doc_number', 'desc')->orderby('sys_receipt.receipt_date', 'desc')->get();


            $receipt2 = $query2->orderby('sys_receipt.doc_number', 'desc')->orderby('sys_receipt.receipt_date', 'desc')->get();
            $receipt = $receipt->merge($receipt2);

            $page = "deal_track";

            return view('backEnd.receipt.receiptadd', compact('accounts', 'receipt_mode_list', 'staff_list', 'receiptmode_cash', 'receiptmode_bank', 'currency', 'deal_id', 'mode_id', 'page_id', 'company', 'account_id', 'account_code', 'account_name', 'amount', 'receipt', 'page'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function getrecustlist()
    {
        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];
        $accounts = SysHelper::get_customer_list($company_id);
        $searchData = [];
        foreach ($accounts as $item) {
            $searchData[] = ['id' => $item->id, 'name' => $item->account_name];
        }

        if (!empty($searchData)) {
            return json_encode($searchData);
        }
    }

    public function getReceiptAttachments($receiptId)
    {
        try {
            if (empty($receiptId) || $receiptId == 0) {
                $attachments = SysReceiptAttachment::where('sys_receipt_id', 0)
                    ->where('created_by', Auth::user()->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                $attachments = SysReceiptAttachment::where('sys_receipt_id', $receiptId)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            return response()->json(['success' => true, 'attachments' => $attachments]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function uploadReceiptAttachments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sys_receipt_id' => 'nullable|integer',
            'files.*' => 'required|file|max:8192|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,txt',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $receiptId = intval($request->input('sys_receipt_id', 0));
        if ($receiptId < 0) {
            $receiptId = 0;
        }

        foreach ($request->file('files', []) as $file) {
            $fileName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $dest = public_path('uploads/receipt_attachments');
            if (!file_exists($dest)) {
                mkdir($dest, 0755, true);
            }
            $file->move($dest, $fileName);
            $storedPath = 'uploads/receipt_attachments/' . $fileName;

            SysReceiptAttachment::create([
                'sys_receipt_id' => $receiptId,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $storedPath,
                'file_type' => $file->getClientOriginalExtension(),
                'created_by' => Auth::user()->id,
            ]);
        }

        if ($receiptId === 0) {
            $allAttachments = SysReceiptAttachment::where('sys_receipt_id', 0)
                ->where('created_by', Auth::user()->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $allAttachments = SysReceiptAttachment::where('sys_receipt_id', $receiptId)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json(['success' => true, 'attachments' => $allAttachments]);
    }

    public function deleteReceiptAttachment($id)
    {
        try {
            $attachment = SysReceiptAttachment::findOrFail($id);
            $storedPath = str_replace('\\', '/', $attachment->file_path);
            $storedPath = preg_replace('#^public/#', '', $storedPath);
            $path = public_path($storedPath);

            if (file_exists($path)) {
                @unlink($path);
            }
            $attachment->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function downloadReceiptAttachment($id)
    {
        try {
            $attachment = SysReceiptAttachment::findOrFail($id);
            $storedPath = str_replace('\\', '/', $attachment->file_path);
            $storedPath = preg_replace('#^public/#', '', $storedPath);
            $path = public_path($storedPath);
            if (!file_exists($path)) {
                abort(404);
            }
            return response()->download($path, $attachment->file_name);
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if (count(array_filter($request->account_id)) > 0 && count(array_filter($request->amount)) > 0) {

                $re = new SysReceipt();
                if ($request->mode == 1) { //mode 1 cash, mode 2 bank
                    $re->doc_number = SysHelper::get_new_code('sys_receipt', 'CR', 'doc_number');
                } else {
                    $re->doc_number = SysHelper::get_new_code('sys_receipt', 'BR', 'doc_number');
                }
                $re->doc_date = Carbon::createFromFormat('d/m/Y', $request->doc_date)->format('Y-m-d');
                $re->mode = $request->mode;
                if ($request->mode == 1) {
                    $re->receipt_mode = $request->receipt_mode_cash;
                    $re->receipt_through = 1;
                } else {
                    $re->receipt_mode = $request->receipt_mode_bank;
                    $re->receipt_through = $request->receipt_through;
                }
                $re->cheque_date = SysHelper::normalizeToYmd($request->cheque_date);

                $re->cheque_number = $request->cheque_number;
                $re->cheque_bank_name = $request->cheque_bank_name;
                $re->currency = $request->currency;
                $re->receipt_date = Carbon::createFromFormat('d/m/Y', $request->receipt_date)->format('Y-m-d');
                $re->narration = $request->narration;
                $re->status = 1;
                $re->created_by = Auth::user()->id;
                $re->created_at = Carbon::now('+04:00');
                $re->company_id = session('logged_session_data.company_id');

                $dealid = explode(',', $request->deal_id);
                
                foreach ($dealid as $d) {
                    $dels[] = SysHelper::get_dealid_from_code($d);
                }
                $re->deal_id = implode(',', $dels);

                $firstDealId = $dels[0];

                $results = $re->save();
                $re->toArray();

                if ($request->mode == 1) { //mode 1 cash, mode 2 bank
                    $account_id = $request->receipt_mode_cash;
                    $transaction_type = "cashreceipt";
                } else {
                    $account_id = $request->receipt_mode_bank;
                    $transaction_type = "bankreceipt";
                }

                $status = 1;
                if ($request->receipt_through == 3 && $request->mode == 2) {
                    $status = 3;
                }

                $array_sum_amount = array_sum(
                    array_map(function ($value) {
                        return (float) str_replace(',', '', $value);
                    }, $request->amount)
                );

                SysHelper::trn_chartof_accounts_transaction_with_main($account_id, $re->id, $re->doc_number, $re->receipt_date, $transaction_type, $array_sum_amount, '0.00', $request->narration, $status, 0, "", 1, 1);
                //return $request->all();
                for ($i = 0; $i < count($request->account_id); $i++) {
                    if ($request->account_id[$i] != "" && $request->amount[$i] != "") {
                        SysHelper::trn_chartof_accounts_transaction_with_main($request->account_id[$i], $re->id, $re->doc_number, $re->receipt_date, $transaction_type, '0.00', str_replace(',', '', $request->amount[$i]), $request->remarks[$i], $status, 0, "", 1, 0);
                    }
                }

                $inv_n = "";

                $temp = SysReceiptAdjustmentsTemp::where('process_id', $request->process_id)->get();
                if (count($temp) > 0) {
                    foreach ($temp as $te) {
                        $inv_n = $te->bi_doc_no;
                        $temp_data[] = [
                            'transaction_type' => $te->transaction_type,
                            'bi_cheque_amount' => $te->bi_cheque_amount,
                            'bi_amount_adjusted' => $te->bi_amount_adjusted,
                            'bi_balance_to_adjust' => $te->bi_balance_to_adjust,
                            'bi_extra_amount' => $te->bi_extra_amount,
                            'bi_currency' => $te->bi_currency,
                            'bi_doc_number' => $re->doc_number,
                            'bi_contains' => $te->bi_contains,
                            'bi_doc_no' => $te->bi_doc_no,
                            'bi_lpo_no' => $te->bi_lpo_no,
                            'bi_doc_date' => $te->bi_doc_date,
                            'bi_total' => $te->bi_total,
                            'bi_paid' => $te->bi_paid,
                            'bi_balance' => $te->bi_balance,
                            'bi_amount' => $te->bi_amount,
                            'bi_narration' => $te->bi_narration,
                            'account_id' => $te->account_id,
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                            'created_at' => Carbon::now('+04:00'),
                            'company_id' => session('logged_session_data.company_id'),
                        ];
                    }
                    SysReceiptAdjustments::insert($temp_data);
                    SysReceiptAdjustmentsTemp::where('process_id', $request->process_id)->delete();
                    SysReceiptAdjustmentsTemp::where('created_by', Auth::user()->id)->delete();
                }

                DB::commit();



                if ($request->has('temp_attachment_ids')) {
                    $attachmentIds = array_filter((array) $request->temp_attachment_ids);
                    if (count($attachmentIds) > 0) {
                        SysReceiptAttachment::whereIn('id', $attachmentIds)
                            ->where(function ($query) {
                                $query->where('sys_receipt_id', 0)
                                      ->orWhereNull('sys_receipt_id');
                            })
                            ->where('created_by', Auth::user()->id)
                            ->update(['sys_receipt_id' => $re->id]);
                    }
                }

                $deal_data = SysCrmDeals::find($request->base_deal_id);
                $user = Auth::user();
                $staff = SmStaff::where('user_id', $user->id)->first();

                $data = [
                    'owner_id' => $deal_data->owner ?? null,
                    'owner_name' => $staff ? $staff->first_name . ' ' . $staff->last_name : null,
                    'owner_email' => $staff->email ?? null,
                    'payment_mode' => $deal_data->payment_mode ?? null,
                    'payment_collection' => 1,
                    'credit_note' => "",
                    'payment_status' => 1,
                    'reminder_date' => "",
                    'reminder_time' => "",
                    'doc_number' => $re->doc_number ?? null,
                    'receipt_mode_txt' => ($request->mode == 1) ? "Cash in Hand" : "RAK Bank",
                    'receipt_mode' => $account_id ?? null,
                    'receipt_date' => $re->receipt_date ?? null,
                    'invoice_no' => $inv_n ?? null,
                    'amount' => $array_sum_amount ?? 0,
                    'receipt_through' => $request->receipt_through ?? null,
                    'payment_mode_sec' => "",
                    'cheque_date' => $re->cheque_date ?? null,
                    'cheque_no' => $re->cheque_number ?? null,
                    'bank_name' => $re->cheque_bank_name ?? null,
                    'remarks' => $request->narration ?? null,
                    'deal_id' => $request->base_deal_id ?? null,
                    'deal_track_id' => SysCrmDealTrack::where('deal_id', $request->base_deal_id)->value('id'),
                    'approval_exists' => DB::table('sys_crm_deal_track_approval_receivables')
                        ->where('deal_id', $request->deal_id)
                        ->exists(),
                ];



                if ($request->deal_track == "deal_track") {
                    $receiavbles_approved = $this->crmdealtrackapprovalreceivables($data);
                }




                Toastr::success('Operation successful', 'Success');
                if ($request->page_id == "cashbook") {
                    return redirect('cashbook');
                } elseif ($request->page_id == "bankbook") {
                    return redirect('bankbook');
                } else {
                    if ($request->deal_page == "1" && !empty($firstDealId) && !empty($request->deal_id)) {
                        $id = SysCrmDealTrack::where('deal_id', $firstDealId)->value('id');
                        return redirect('crm-deal-track-approval-list/' . $id);
                    }
                    return redirect('receipt/' . $re->id);
                }
            } else {
                Toastr::error('Operation Failed. please enter valid data', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function crmdealtrackapprovalreceivables($data)
    {
        $trn_time = Carbon::now('Asia/Dubai')->format('Y-m-d H:i:s');

        try {

            /* -----------------------------------------
             | STATUS LOGIC
             |------------------------------------------*/
            $status = 1;
            $payment_status = 0;
            $amount = 0;

            if ($data['payment_collection'] == 2) {
                $status = 2;
            }

            if ($data['payment_collection'] == 3) {
                $status = 4;
            }

            if ($data['payment_collection'] != 3) {
                $payment_status = $data['payment_status'] ?? 0;
                $amount = $data['amount'] ?? 0;

                if ($payment_status == 2) {
                    $status = 3;
                }
            }

            /* -----------------------------------------
             | CHECK EXISTING RECORD
             |------------------------------------------*/
            $rec = SysCrmDealTrackApprovalReceivables::where('deal_id', $data['deal_id'])->first();

            /* -----------------------------------------
             | REMINDER DATE FORMAT
             |------------------------------------------*/
            $reminderDate = null;
            if (!empty($data['reminder_date'])) {
                $reminderDate = Carbon::createFromFormat(
                    'd/m/Y H:i',
                    $data['reminder_date'] . ' ' . $data['reminder_time']
                )->format('Y-m-d H:i:s');
            }

            /* -----------------------------------------
             | COMMON DATA
             |------------------------------------------*/
            $payload = [
                'deal_track_id' => $data['deal_track_id'],
                'deal_id' => $data['deal_id'],
                'payment_collection' => $data['payment_collection'],
                'payment_status' => $payment_status,
                'reminder_date' => $reminderDate,
                'remarks' => $data['remarks'],
                'status' => $status,
                'updated_by' => Auth::id(),
                'updated_at' => $trn_time,
                'paymenttype' => $data['payment_mode'],
                'amount' => $amount,
                'doc_number' => $data['doc_number'],
                'receipt_mode' => $data['receipt_mode'] ?? 0,
                'receipt_date' => SysHelper::normalizeToYmd($data['receipt_date']),
                'invoice_no' => $data['invoice_no'],
                'receipt_through' => $data['receipt_through'],
                'cheque_date' => SysHelper::normalizeToYmd($data['cheque_date']),
                'cheque_no' => $data['cheque_no'],
                'bank_name' => $data['bank_name'],
                'credit_note' => $data['credit_note'],
            ];

            /* -----------------------------------------
             | UPDATE OR CREATE
             |------------------------------------------*/
            if ($rec) {
                $rec->update($payload);
            } else {
                $payload['created_by'] = Auth::id();
                $payload['created_at'] = $trn_time;
                $payload['created_date'] = $trn_time;

                SysCrmDealTrackApprovalReceivables::create($payload);
            }

            /* -----------------------------------------
             | UPDATE DEAL TRACK
             |------------------------------------------*/
            DB::table('sys_crm_deal_track')
                ->where('deal_id', $data['deal_id'])
                ->update([
                    'receivables' => $status,
                    'receivables_approval' => 1
                ]);

            /* -----------------------------------------
             | NOTIFICATIONS
             |------------------------------------------*/
            if ($status == 2) {

                SysHelper::exe_web_push(
                    $data['owner_id'],
                    'Deal Track Rejected',
                    'Deal ' . $data['deal_id'] . ' Rejected',
                    'crm-deal-track/' . $data['deal_id'] . '/view'
                );

                SysHelper::Erp_Notify_track_reject(
                    $data['deal_id'],
                    $data['owner_name'],
                    $data['owner_email'],
                    "Receivables",
                    $data['remarks']
                );

                Toastr::error('Rejected successfully', 'Rejected');
            } elseif ($status == 1) {

                SysHelper::set_deal_profit($data['deal_id']);

                SysHelper::exe_web_push(
                    $data['owner_id'],
                    'Deal Track Completed',
                    'Deal ' . $data['deal_id'],
                    'crm-deal-track-approval/' . $data['deal_track_id']
                );

                Toastr::success('Approved successfully', 'Success');
            } else {
                Toastr::warning('Updated successfully', 'Updated');
            }

            return true;

        } catch (\Throwable $th) {

            Toastr::error('Operation Failed', 'Failed');
            return false;
        }
    }


    public function edit(Request $request, $id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $accounts = SysHelper::get_customer_list_all($company_id);
            $receiptmode_cash = SysHelper::get_cash_account('all');
            $receiptmode_bank = SysHelper::get_bank_account('all');
            $currency = SysCurrencySettings::select('id', 'code')->get();

            $editData = SysReceipt::find($id);

            $dealid = explode(',', $editData->deal_id);
            foreach ($dealid as $d) {
                $dels[] = SysHelper::get_code_from_dealid($d);
            }
            $deal_id = implode(',', $dels);

            $data1 = $receiptmode_cash->pluck('id');
            $data2 = $receiptmode_bank->pluck('id');
            $data3 = array_merge($data1->toArray(), $data2->toArray());
            $accounts = SysHelper::get_customer_list($company_id);
            $receipt_mode_list = array_merge($receiptmode_cash->toArray(), $receiptmode_bank->toArray());

            $staff_list = SysHelper::get_staff_list();

            $editDataList = DB::table('sys_chartofaccounts_transaction as t')->select('t.id', 't.transaction_no', 't.account_id', 't.credit_amount', 't.remarks', DB::raw('COALESCE(SUM(ra.bi_paid), 0) AS adj_amount'), 'c.account_name', 'c.account_code')
                ->leftjoin('sys_receipt_adjustments as ra', 'ra.bi_doc_number', 't.transaction_no')
                ->leftjoin('sys_chartofaccounts as c', 'c.id', 't.account_id')
                ->where('transaction_id', $id)->wherein('t.transaction_type', ['cashreceipt', 'bankreceipt'])->where('is_main_account', 0)
                ->groupby('t.id', 't.transaction_no', 't.account_id', 't.credit_amount', 't.remarks')->get();


            //$editDataAdjustments = SysReceiptAdjustments::where('bi_doc_number', $editData->bi_doc_number)->get();
            $editDataAdjustments = SysReceiptAdjustments::where('bi_doc_number', $editData->doc_number)->where('status', 1)->get();

            $query = SysReceipt::select('sys_receipt.id', 'sys_receipt.doc_number', 'sys_receipt.mode', 'sys_receipt.receipt_mode', 'sys_receipt.receipt_through', 'a.account_name', 'c.debit_amount', 'c.credit_amount', 'sys_receipt.doc_date', 'sys_receipt.receipt_date', 'sys_receipt.cheque_date', 'sys_receipt.cheque_number', 'sys_receipt.cheque_bank_name', 'u.full_name', 'sys_receipt.narration', 'sys_receipt.status', 'sys_receipt.deal_id')->selectRaw('1 as type')
                ->leftjoin('sys_chartofaccounts_transaction as c', 'c.transaction_no', 'sys_receipt.doc_number')
                ->leftjoin('sys_chartofaccounts as a', 'a.id', 'c.account_id')
                ->leftjoin('users as u', 'u.id', 'sys_receipt.created_by');

            $doc = SysChartofAccountsTransaction::wherein('transaction_type', ['bankreceipt', 'cashreceipt'])->wherein('company_id', $company_id)->wherenotin('account_id', $data3)->pluck('transaction_no');
            $query2 = SysReceipt::select('sys_receipt.id', 'sys_receipt.doc_number', 'sys_receipt.mode', 'sys_receipt.receipt_mode', 'sys_receipt.receipt_through', 'sys_receipt.doc_date', 'sys_receipt.receipt_date', 'sys_receipt.cheque_date', 'sys_receipt.cheque_number', 'sys_receipt.cheque_bank_name', 'u.full_name', 'sys_receipt.narration', 'sys_receipt.status', 'sys_receipt.deal_id')->selectRaw('2 as type')
                ->leftjoin('users as u', 'u.id', 'sys_receipt.created_by')
                ->wherenotin('doc_number', $doc)->wherein('sys_receipt.company_id', $company_id);
            $query->wherenotin('c.account_id', $data3);
            $query->wherein('sys_receipt.company_id', $company_id);
            $query->orderby('sys_receipt.id', 'desc');
            $receipt = $query->orderby('sys_receipt.doc_number', 'desc')->orderby('sys_receipt.receipt_date', 'desc')->get();


            $receipt2 = $query2->orderby('sys_receipt.doc_number', 'desc')->orderby('sys_receipt.receipt_date', 'desc')->get();
            $receipt = $receipt->merge($receipt2);

            

            return view('backEnd.receipt.receiptedit', compact('accounts', 'receipt_mode_list', 'staff_list', 'receiptmode_cash', 'receiptmode_bank', 'currency', 'editData', 'editDataList', 'editDataAdjustments', 'deal_id', 'receipt'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function view(Request $request, $id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $accounts = SysHelper::get_customer_list_all($company_id);
            $receiptmode_cash = SysHelper::get_cash_account('all');
            $receiptmode_bank = SysHelper::get_bank_account('all');
            $currency = SysCurrencySettings::select('id', 'code')->get();

            $editData = SysReceipt::find($id);
            $editDataList = SysChartofAccountsTransaction::where('transaction_id', $id)->wherein('transaction_type', ['cashreceipt', 'bankreceipt'])->where('is_main_account', 0)->get();

            $editDataAdjustments = SysReceiptAdjustments::where('bi_doc_number', $editData->doc_number)->where('status', 1)->get();

            return view('backEnd.receipt.receiptview', compact('accounts', 'receiptmode_cash', 'receiptmode_bank', 'currency', 'editData', 'editDataList', 'editDataAdjustments'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function download(Request $request, $id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $receipt = SysReceipt::find($id);
            $receiptList = SysChartofAccountsTransaction::where('transaction_id', $id)->wherein('transaction_type', ['cashreceipt', 'bankreceipt'])->where('is_main_account', 0)->get();
            $company = SysCompany::find($receipt->company_id);
            $print = date('d/m/Y h:i A', strtotime(Carbon::now('+04:00')));

            $data = [
                'company' => $company,
                'receipt' => $receipt,
                'receipt_item' => $receiptList,
                'print' => $print,
            ];


            $pdf = PDF::loadView('backEnd.pdf_print.receipt_pdf', $data);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download($receipt->doc_number . ".pdf");

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $re = SysReceipt::find($id);
            $re->doc_date = Carbon::createFromFormat('d/m/Y', $request->doc_date)->format('Y-m-d');
            $re->mode = $request->mode;
            if ($request->mode == 1) {
                if ($request->mode != $request->actual_mode) {
                    $doc = SysHelper::get_new_code('sys_receipt', 'CR', 'doc_number');
                    $re->edit_note = $re->doc_number . " change to " . $doc;
                    $re->doc_number = $doc;
                    SysReceiptAdjustments::where('bi_doc_number', $re->doc_number)->delete();
                }
                $re->receipt_mode = $request->receipt_mode_cash;
                $re->receipt_through = 1;

            } else {
                if ($request->mode != $request->actual_mode) {
                    $doc = SysHelper::get_new_code('sys_receipt', 'BR', 'doc_number');
                    $re->edit_note = $re->doc_number . " change to " . $doc;
                    $re->doc_number = $doc;
                    SysReceiptAdjustments::where('bi_doc_number', $re->doc_number)->delete();
                }
                $re->receipt_mode = $request->receipt_mode_bank;
                $re->receipt_through = $request->receipt_through;
            }

            $re->cheque_date = SysHelper::normalizeToYmd($request->cheque_date);
            // $re->cheque_date = date('Y-m-d', strtotime($request->cheque_date));
            $re->cheque_number = $request->cheque_number;
            $re->cheque_bank_name = $request->cheque_bank_name;
            $re->currency = $request->currency;
            if ($re->receipt_date != Carbon::createFromFormat('d/m/Y', $request->receipt_date)->format('Y-m-d')) {
                $re->pdc_removed_os = 1;
            }
            $re->receipt_date = Carbon::createFromFormat('d/m/Y', $request->receipt_date)->format('Y-m-d');
            $re->narration = $request->narration;
            $re->status = 1;
            $re->updated_by = Auth::user()->id;
            $re->updated_at = Carbon::now('+04:00');

            //$re->company_id = session('logged_session_data.company_id');
            //$re->deal_id = SysHelper::get_dealid_from_code($request->deal_id);



            $dealid = explode(',', $request->deal_id);

            // remove spaces from deal codes
            $dealid = array_map('trim', $dealid);

   

            
            
     
            foreach ($dealid as $d) {
                $dels[] = SysHelper::get_dealid_from_code($d);
            
            }
          
           
            $re->deal_id = implode(',', $dels);


            $results = $re->save();
            $re->toArray();

            if ($request->mode == 1) { //mode 1 cash, mode 2 bank
                $account_id = $request->receipt_mode_cash;
                $transaction_type = "cashreceipt";
            } else {
                $account_id = $request->receipt_mode_bank;
                $transaction_type = "bankreceipt";
            }

            SysChartofAccountsTransaction::query()
                ->where('transaction_id', $id)->wherein('transaction_type', ['cashreceipt', 'bankreceipt'])
                ->each(function ($oldRecord) {
                    $newRecord = $oldRecord->replicate();
                    $newRecord->setTable('sys_chartofaccounts_transaction_history');
                    $newRecord->save();
                    $oldRecord->delete();
                });


            $status = 1;
            if ($request->receipt_through == 3 && $request->mode == 2) {
                $status = 3;
            }
            $array_sum_amount = array_sum(
                array_map(function ($value) {
                    return (float) str_replace(',', '', $value);
                }, $request->amount)
            );

            SysHelper::trn_chartof_accounts_transaction_with_main($account_id, $re->id, $re->doc_number, $re->receipt_date, $transaction_type, $array_sum_amount, '0.00', $request->narration, $status, 0, "", 1, 1);
            //return $request->all();

            for ($i = 0; $i < count($request->account_id); $i++) {
                if ($request->account_id[$i] != "" && $request->amount[$i] != "") {
                    SysHelper::trn_chartof_accounts_transaction_with_main($request->account_id[$i], $re->id, $re->doc_number, $re->receipt_date, $transaction_type, '0.00', str_replace(',', '', $request->amount[$i]), $request->remarks[$i], $status, 0, "", 1, 0);
                }
            }

            if ($request->has('temp_attachment_ids')) {
                $attachmentIds = array_filter((array) $request->temp_attachment_ids);
                if (count($attachmentIds) > 0) {
                    SysReceiptAttachment::whereIn('id', $attachmentIds)
                        ->where(function ($query) {
                            $query->where('sys_receipt_id', 0)
                                  ->orWhereNull('sys_receipt_id');
                        })
                        ->where('created_by', Auth::user()->id)
                        ->update(['sys_receipt_id' => $re->id]);
                }
            }

            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect('receipt/' . $id);

        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function receiptdateupdate(Request $request)
    {
        try {

            DB::beginTransaction();
            $request->receipt_date = SysHelper::normalizeToYmd($request->receipt_date);


            if (substr($request->receipt_id, 0, 2) == 'JV') {


            } else if (substr($request->receipt_id, 0, 2) == 'CR') {
                SysReceipt::where('doc_number', $request->receipt_id)->update(['receipt_date' => $request->receipt_date]);
                SysChartofAccountsTransaction::where('transaction_no', $request->receipt_id)->wherein('transaction_type', ['bankreceipt', 'cashreceipt'])->update(['transaction_date' => $request->receipt_date]);

            } else if (substr($request->receipt_id, 0, 2) == 'BR') {
                SysReceipt::where('doc_number', $request->receipt_id)->update(['receipt_date' => $request->receipt_date]);
                SysChartofAccountsTransaction::where('transaction_no', $request->receipt_id)->wherein('transaction_type', ['bankreceipt', 'cashreceipt'])->update(['transaction_date' => $request->receipt_date]);


            } else if (substr($request->receipt_id, 0, 2) == 'CP') {
                SysPayment::where('doc_number', $request->receipt_id)->update(['payment_date' => $request->receipt_date]);
                SysChartofAccountsTransaction::where('transaction_no', $request->receipt_id)->wherein('transaction_type', ['bankpayment', 'cashpayment'])->update(['transaction_date' => $request->receipt_date]);


            } else if (substr($request->receipt_id, 0, 2) == 'BP') {
                SysPayment::where('doc_number', $request->receipt_id)->update(['payment_date' => $request->receipt_date]);
                SysChartofAccountsTransaction::where('transaction_no', $request->receipt_id)->wherein('transaction_type', ['bankpayment', 'cashpayment'])->update(['transaction_date' => $request->receipt_date]);

            }




            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }
    public function delete_adjustment($id)
    {
        try {
            DB::table('sys_receipt_adjustments')->where('id', $id)->delete();

            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function delete_adjustment_json(Request $request)
    {
        try {
            DB::table('sys_receipt_adjustments')->where('id', $request->id)->delete();
            $ret = SysReceiptAdjustments::where('bi_doc_number', $request->doc_number)->where('status', 1)->get();
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


    // public function receiptadjustmentsstore(Request $request)
    // {
    //     try{
    //     $sra = new SysReceiptAdjustments();
    //     $sra->bi_new_reference = $request->bi_new_reference;
    //     $sra->bi_amount_to_adjust = $request->bi_amount_to_adjust;
    //     $sra->bi_adjusted_amount = $request->bi_adjusted_amount;
    //     $sra->bi_currency = $request->bi_currency;
    //     $sra->bi_doc_number = $request->bi_doc_number;
    //     $sra->bi_contains = $request->bi_contains;
    //     $sra->bi_doc_no = $request->bi_doc_no;
    //     $sra->bi_doc_date = date('Y-m-d', strtotime($request->bi_doc_date));
    //     $sra->bi_lpo_no = $request->bi_lpo_no;
    //     $sra->bi_due_date = date('Y-m-d', strtotime($request->bi_due_date));
    //     $sra->bi_total = $request->bi_total;
    //     $sra->bi_paid = $request->bi_amount;
    //     $sra->bi_balance = $request->bi_balance;
    //     $sra->bi_amount = $request->bi_amount;
    //     $sra->status = 1;
    //     $sra->created_by = Auth::user()->id;
    //     $results = $sra->save();
    //     $sra->toArray();

    //     $sle = new SysLedgerEntries();
    //     $sle->transaction_id = $request->bi_doc_number;
    //     $sle->transaction_type = "bankreceipt";
    //     $sle->account_id = $request->account_id;
    //     $sle->entry_date = date('Y-m-d', strtotime($request->entry_date));
    //     $sle->entry_type = 1; //Debit
    //     $sle->amount = $request->bi_amount;
    //     $sle->status = 1;
    //     $sle->created_by = Auth::user()->id;
    //     $sle->save();

    //     $ret = 'SUCCESS';
    //         return json_encode(array('data'=>$ret));
    //     }catch (\Exception $e) {
    //         $ret = $e;
    //         return json_encode(array('data'=>$ret));
    //     }
    // }

    public function getrebalancelist(Request $request)
    {
        try {
            $company_id = session('logged_session_data.company_id');
            $opb = SysChartofAccountsTransaction::wherein('transaction_type', ['openingbalance11111', 'opbinvoice'])->where('status', 1)->where('account_id', $request->account_id)->where('company_id', $company_id)->get();
            $items = DB::select("CALL get_bank_receipt_adjestments($request->account_id,$company_id)");

            // $items = SysSalesInvoice::select('sys_sales_invoice.doc_number', 'sys_sales_invoice.si_date', 'sys_sales_invoice.lpo_number','sys_sales_invoice.lpo_date', DB::raw('SUM(sys_sales_invoice_items.taxableamount) as amount'))
            // ->join('sys_sales_invoice_items', 'sys_sales_invoice.id', '=', 'sys_sales_invoice_items.si_id')
            // ->where('sys_sales_invoice.customer',$request->cr_account_id)
            // ->groupBy('sys_sales_invoice.id')
            // ->groupBy('sys_sales_invoice.doc_number')
            // ->groupBy('sys_sales_invoice.si_date')
            // ->groupBy('sys_sales_invoice.lpo_number')
            // ->groupBy('sys_sales_invoice.lpo_date')
            // ->get();

            //$items = SysCustSuppl::select('id','name')->where('catid',1)->get();

            $searchData = [];

            if (count($opb) > 0) {
                foreach ($opb as $dt) {
                    $paid = SysReceiptAdjustments::where('bi_doc_no', $dt->transaction_no)->sum('bi_paid');
                    $searchData[] = [
                        'deal_id' => SysHelper::get_salesinvoice_deal_code($dt->transaction_no),
                        'doc_number' => $dt->transaction_no,
                        'doc_date' => $dt->transaction_date,
                        'lpo_number' => '',
                        'lpo_date' => '',
                        'total' => abs($dt->debit_amount - $dt->credit_amount),
                        'paid' => $paid,
                        'balance' => abs($dt->debit_amount - $dt->credit_amount) - $paid,
                    ];
                }
            }


            foreach ($items as $item) {
                $searchData[] = [
                    'deal_id' => SysHelper::get_salesinvoice_deal_code($item->doc_number),
                    'doc_number' => $item->doc_number,
                    'doc_date' => $item->doc_date,
                    'lpo_number' => $item->lpo_number,
                    'lpo_date' => $item->lpo_date,
                    'total' => $item->total,
                    'paid' => $item->paid,
                    'balance' => $item->balance,
                ];
            }

            // if(!empty($searchData)){
            // 	return json_encode($searchData);
            // }
            $positiveUnadjusted = SysHelper::get_positive_receivable_unadjusted_for_billwise($request->account_id, $company_id);
            $invoiceDocNumbers = collect($searchData)->pluck('doc_number')->filter()->unique();
            $positiveUnadjusted = collect($positiveUnadjusted)
                ->reject(function ($row) use ($invoiceDocNumbers) {
                    return $invoiceDocNumbers->contains($row->doc_number);
                })
                ->values();

            return response()->json([
                'invoices' => $searchData,
                'positive_unadjusted' => $positiveUnadjusted,
            ]);


        } catch (\Throwable $th) {
            return json_encode($th);
        }
    }

    public function getrebalancelistedit(Request $request)
    {
        $company_id = session('logged_session_data.company_id');
        $opb = SysChartofAccountsTransaction::wherein('transaction_type', ['openingbalance11111', 'opbinvoice'])->where('account_id', $request->account_id)->where('status', 1)->where('company_id', $company_id)->get();
        $items = DB::select("CALL get_bank_receipt_adjestments_edit($request->account_id,$company_id)");

        $searchData = [];

        $adjestData = SysReceiptAdjustments::where('bi_doc_number', $request->doc_number)->where('account_id', $request->account_id)->get();

        if (count($opb) > 0) {
            foreach ($opb as $dt) {
                $paid = SysReceiptAdjustments::where('bi_doc_no', $dt->transaction_no)->where('account_id', $request->account_id)->sum('bi_paid');
                $bi_amount = $adjestData->where('bi_doc_no', $dt->transaction_no)->sum('bi_paid');
                if ($bi_amount != 0) {
                    $paid = 0;
                }
                $searchData[] = [
                    'deal_id' => '',
                    'doc_number' => $dt->transaction_no,
                    'doc_date' => $dt->transaction_date,
                    'lpo_number' => '',
                    'lpo_date' => '',
                    'total' => abs($dt->debit_amount - $dt->credit_amount),
                    'paid' => $paid,
                    'bi_amount' => $bi_amount,
                    'balance' => abs($dt->debit_amount - $dt->credit_amount) - $paid,
                ];
            }
        }


        foreach ($items as $item) {
            $paid = $item->paid;
            $bi_amount = $adjestData->where('bi_doc_no', $item->doc_number)->sum('bi_paid');
            if ($bi_amount != 0) {
                $paid = 0;
            }
            if ($item->balance > 0 || $bi_amount > 0) {
                $searchData[] = [
                    'deal_id' => SysHelper::get_salesinvoice_deal_code($item->doc_number),
                    'doc_number' => $item->doc_number,
                    'doc_date' => $item->doc_date,
                    'lpo_number' => $item->lpo_number,
                    'lpo_date' => $item->lpo_date,
                    'total' => $item->total,
                    'paid' => $paid,
                    'bi_amount' => $bi_amount,
                    'balance' => $item->balance,
                ];
            }
        }
        $currentAdjustedByDoc = $adjestData
            ->groupBy('bi_doc_no')
            ->map(function ($group) {
                return (float) $group->sum('bi_paid');
            });
        $positiveUnadjusted = SysHelper::get_positive_receivable_unadjusted_for_billwise($request->account_id, $company_id, $currentAdjustedByDoc);
        $invoiceDocNumbers = collect($searchData)->pluck('doc_number')->filter()->unique();
        $positiveUnadjusted = collect($positiveUnadjusted)
            ->reject(function ($row) use ($invoiceDocNumbers) {
                return $invoiceDocNumbers->contains($row->doc_number);
            })
            ->values();

        return response()->json([
            'invoices' => $searchData,
            'positive_unadjusted' => $positiveUnadjusted,
        ]);
    }

    public function delete($id)
    {
        try {
            DB::table('sys_receipt')->where('id', $id)->update(['status' => 2]);
            DB::table('sys_chartofaccounts_transaction')->where('transaction_id', $id)->wherein('transaction_type', ['bankreceipt', 'cashreceipt'])->update(['status' => 2]);

            $dt = DB::table('sys_receipt')->where('id', $id)->first();
            //DB::table('sys_receipt_adjustments')->where('bi_doc_number',$dt->doc_number)->update(['status' => 2]);
            DB::table('sys_receipt_adjustments')->where('bi_doc_number', $dt->doc_number)->delete();

            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function restore($id)
    {
        try {
            DB::table('sys_receipt')->where('id', $id)->update(['status' => 1]);
            DB::table('sys_chartofaccounts_transaction')->where('transaction_id', $id)->wherein('transaction_type', ['bankreceipt', 'cashreceipt'])->update(['status' => 1]);

            $dt = DB::table('sys_receipt')->where('id', $id)->first();
            //DB::table('sys_receipt_adjustments')->where('bi_doc_number',$dt->doc_number)->update(['status' => 1]);

            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function delete_receipt_items(Request $request)
    {
        try {
            db::beginTransaction();
            SysChartofAccountsTransaction::where(['id' => $request->id])->delete();
            SysReceiptAdjustments::where(['account_id' => $request->account_id])->where('bi_doc_number', $request->transaction_no)->delete();

            $amount = SysChartofAccountsTransaction::where(['transaction_no' => $request->transaction_no])->sum('credit_amount');
            SysChartofAccountsTransaction::where(['transaction_no' => $request->transaction_no])
                ->where(['is_main_account' => 1])->where(['credit_amount' => '0.00'])->update(['debit_amount' => $amount]);

            db::commit();

            $ret = 'SUCCESS';
            return json_encode(array('data' => $ret));

        } catch (\Exception $e) {
            db::rollBack();
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    // public function deleteSupplier(Request $request,$id){

    //     try{
    //         $result = SmSupplier::destroy($id);

    //         if (ApiBaseMethod::checkUrl($request->fullUrl())) {
    //             if ($result) {
    //                 return ApiBaseMethod::sendResponse(null, 'Supplier has been deleted successfully');
    //             } else {
    //                 return ApiBaseMethod::sendError('Something went wrong, please try again.');
    //             }
    //         } else {
    //             if ($result) {
    //                 Toastr::success('Operation successful', 'Success');
    //                 return redirect('suppliers');
    //             } else {
    //                 Toastr::error('Operation Failed', 'Failed');
    //                 return redirect()->back();
    //             }
    //         }
    //     }catch (\Exception $e) {
    //        Toastr::error('Operation Failed', 'Failed');
    //        return redirect()->back(); 
    //     }
    // }
}

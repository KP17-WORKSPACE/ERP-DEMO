<?php

namespace App\Http\Controllers;

use App\SysChartofAccounts;
use App\SysAccountType;
use App\ApiBaseMethod;
use App\SysCashReceipt;
use App\SysCashReceiptList;
use App\SysCustSuppl;
use App\SysAccountGroup;
use App\SysChartofAccountsTransaction;
use App\SysCompany;
use App\SysCurrencySettings;
use App\SysHelper;
use App\SysJournalVoucher;
use App\SysJournalVoucherList;
use App\SysLedgerEntries;
use App\SysPaymentAdjustments;
use App\SysPaymentAdjustmentsTemp;
use App\SysReceipt;
use App\SysReceiptAdjustments;
use App\SysReceiptAdjustmentsTemp;
use App\SysReceiptMode;
use App\SysSalesReturn;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PayPal\Api\Transactions;
use App\SysCrmDealTrack;
class SysJournalVoucherController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function journalvoucherList(Request $request,$jv_id=null)
    {
        try{
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $documents_number="";
            $filter_by="";
            $ctrl_date="";
            $ctrl_date2="";

            $query = SysJournalVoucher::select('sys_journalvoucher.id','sys_journalvoucher.deal_id','sys_journalvoucher.doc_date','sys_journalvoucher.doc_number','sys_journalvoucher.created_by','sys_journalvoucher.narration','sys_journalvoucher.status',DB::RAW('(SELECT GROUP_CONCAT(doc_file) FROM sys_journalvoucher_att WHERE doc_id = sys_journalvoucher.id) AS attach'),DB::RAW('sum(cat.debit_amount) as debit_amount'),DB::RAW('sum(cat.credit_amount) as credit_amount'))
            ->leftjoin('sys_chartofaccounts_transaction as cat','cat.transaction_no','sys_journalvoucher.doc_number')
            ->wherein('sys_journalvoucher.company_id',$company_id);

            if(SysHelper::get_pagination_post($request)){
                if ($request->from_date != "" && $request->filter_by == "") {
                    $ctrl_date= SysHelper::normalizeToYmd($request->from_date);
                }
                if ($request->to_date != "" && $request->filter_by == "") {
                    $ctrl_date2= SysHelper::normalizeToYmd($request->to_date);
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
                    $query->where('sys_journalvoucher.doc_number','like','%'.$request->documents_number.'%');
                    $documents_number = $request->documents_number;
                }
                if ($ctrl_date != "" && $ctrl_date2 != "") {
                    $query->whereBetween('sys_journalvoucher.doc_date', [$ctrl_date, $ctrl_date2]);
                }                
                if ($ctrl_date != "" && $ctrl_date2 == "") {
                    $query->where('sys_journalvoucher.doc_date',$ctrl_date);
                }
                if ($ctrl_date == "" && $ctrl_date2 != "") {
                    $query->where('sys_journalvoucher.doc_date',$ctrl_date2);
                }
            }

            $journalvoucher = $query->groupby('sys_journalvoucher.id','sys_journalvoucher.deal_id','sys_journalvoucher.doc_date','sys_journalvoucher.doc_number','sys_journalvoucher.created_by','sys_journalvoucher.narration','sys_journalvoucher.status')->orderby('sys_journalvoucher.id','desc')->get();
            

            $active_id = $jv_id;
            $data = [];


            $action = false;
            $editData = [];

            $addData = [];




            if ($request->has('jv_action')) {
                $poAction = $request->input('jv_action');

                if ($poAction === 'add') {
                    $action = 'add';

                    $addData = $this->journalvoucherAddData(); // Get all data for adding

                 
                  

                } elseif ($poAction === 'edit') {
                    $action = 'edit';
                    $editData = $this->editData($active_id); // Get all data for editing
                }
            } else {
                if ($jv_id) {
                    $data = $this->get_journalvoucher_data($jv_id);
                } else {
                    $firstRecord = $journalvoucher->first();
                    if ($firstRecord) {
                        $active_id = $firstRecord->id;
                        $data = $this->get_journalvoucher_data($firstRecord->id);
                    }
                }
            }



            //return $journalvoucher;
            return view('backEnd.journal-voucher.journalvoucherlist', compact('journalvoucher','data','filter_by','ctrl_date','ctrl_date2','documents_number','active_id','action','editData','addData'));
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function getDetails($id)
    {
         $data = $this->get_journalvoucher_data($id);
         if(count($data)>0){
                return view('backEnd.journal-voucher.j_details', $data);
            }
            else {
                return "error!!";
            }

    }

       public function getDetailsPDF($id)
    {
         $data = $this->get_journalvoucher_data($id);
         if(count($data)>0){
                return view('backEnd.journal-voucher.j_details-pdf', $data);
            }
            else {
                return "error!!";
            }

    }

    public function get_journalvoucher_data($id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            
            $vat_account_val = SysHelper::get_purchase_vat_account_id();
            $vat_account_text = SysChartofAccounts::where('id',$vat_account_val)->value('account_name');
            $accounts = SysHelper::get_account_list2();
            $currency = SysCurrencySettings::all();

            $editData = SysJournalVoucher::find($id);
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $editDataList = SysChartofAccountsTransaction::where(['transaction_id' => $id, 'transaction_type' => 'journalvoucher'])->get();
            $editDataAdjustmentsR = SysReceiptAdjustments::where('bi_doc_number', $editData->doc_number)->where('status',1)->get();
            $editDataAdjustmentsP = SysPaymentAdjustments::where('bi_doc_number', $editData->doc_number)->where('status',1)->get();
        
            
            $data = [
                'company' => $company,
                'currency' => $currency,
                'editData' => $editData,
                'editDataList' => $editDataList,
                'editDataAdjustmentsR' => $editDataAdjustmentsR,
                'editDataAdjustmentsP' => $editDataAdjustmentsP,
            ];
            return $data;
        } catch (\Throwable $th) {
            return [];
        }
    }


        public function journalvoucherAddData()
    {
        try{            
            $deal_id=0;
            //$accounts = SysChartofAccounts::select('id','account_name')->wherein('sys_chartofaccounts.company_id',$company_id)->where('status',1)->orderby('account_name','asc')->get();

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $documents_number="";
            $filter_by="";
            $ctrl_date="";
            $page_id=null;
            $ctrl_date2="";

            $vat_account_val = SysHelper::get_purchase_vat_account_id();
            $vat_account_text = SysChartofAccounts::where('id',$vat_account_val)->value('account_name');
            $vat_account_code = SysChartofAccounts::where('id',$vat_account_val)->value('account_code');
            $accounts = SysHelper::get_account_list2();
            $currency = SysCurrencySettings::all();
            $get_staff_list = SysHelper::get_staff_list();

            $company = SysCompany::find(session('logged_session_data.company_id'));
            $editDataList = DB::table('sys_journalvoucher_cart as cart')->select('cart.*','ca.account_code','ca.account_name')
            ->join('sys_chartofaccounts as ca','ca.id','cart.account_id')
            ->where(['cart_id' => session('logged_session_data.cart_id'), 'cart.company_id' => session('logged_session_data.company_id'),'cart.status' => 1])
            ->where('account_id','!=',0)->get();

            db::table('sys_journalvoucher_cart')->where(['cart_id' => session('logged_session_data.cart_id'), 'company_id' => session('logged_session_data.company_id')])
            ->where('account_id','!=',0)->delete();
            
            $journalvoucher = SysJournalVoucher::select('sys_journalvoucher.id','sys_journalvoucher.doc_date','sys_journalvoucher.doc_number','sys_journalvoucher.created_by','sys_journalvoucher.narration','sys_journalvoucher.status',DB::RAW('(SELECT GROUP_CONCAT(doc_file) FROM sys_journalvoucher_att WHERE doc_id = sys_journalvoucher.id) AS attach'),DB::RAW('sum(cat.debit_amount) as debit_amount'),DB::RAW('sum(cat.credit_amount) as credit_amount'))
            ->leftjoin('sys_chartofaccounts_transaction as cat','cat.transaction_no','sys_journalvoucher.doc_number')
            ->wherein('sys_journalvoucher.company_id',$company_id)
            ->groupby('sys_journalvoucher.id','sys_journalvoucher.doc_date','sys_journalvoucher.doc_number','sys_journalvoucher.created_by','sys_journalvoucher.narration','sys_journalvoucher.status')->orderby('sys_journalvoucher.doc_date','desc')->get();

            return compact('accounts','currency','company','deal_id','editDataList','vat_account_val','vat_account_text','vat_account_code','journalvoucher','filter_by','ctrl_date','ctrl_date2','documents_number','get_staff_list','page_id');
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }


    public function journalvoucherAdd(Request $request,$page_id=null)
    {
        try{            
            $deal_id=0;
            //$accounts = SysChartofAccounts::select('id','account_name')->wherein('sys_chartofaccounts.company_id',$company_id)->where('status',1)->orderby('account_name','asc')->get();

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $documents_number="";
            $filter_by="";
            $ctrl_date="";
            $ctrl_date2="";

            $vat_account_val = SysHelper::get_purchase_vat_account_id();
            $vat_account_text = SysChartofAccounts::where('id',$vat_account_val)->value('account_name');
            $vat_account_code = SysChartofAccounts::where('id',$vat_account_val)->value('account_code');
            $accounts = SysHelper::get_account_list2();
            $currency = SysCurrencySettings::all();
            $get_staff_list = SysHelper::get_staff_list();

            $company = SysCompany::find(session('logged_session_data.company_id'));
            $editDataList = DB::table('sys_journalvoucher_cart as cart')->select('cart.*','ca.account_code','ca.account_name')
            ->join('sys_chartofaccounts as ca','ca.id','cart.account_id')
            ->where(['cart_id' => session('logged_session_data.cart_id'), 'cart.company_id' => session('logged_session_data.company_id'),'cart.status' => 1])
            ->where('account_id','!=',0)->get();

            db::table('sys_journalvoucher_cart')->where(['cart_id' => session('logged_session_data.cart_id'), 'company_id' => session('logged_session_data.company_id')])
            ->where('account_id','!=',0)->delete();
            
            $journalvoucher = SysJournalVoucher::select('sys_journalvoucher.id','sys_journalvoucher.doc_date','sys_journalvoucher.doc_number','sys_journalvoucher.created_by','sys_journalvoucher.narration','sys_journalvoucher.status',DB::RAW('(SELECT GROUP_CONCAT(doc_file) FROM sys_journalvoucher_att WHERE doc_id = sys_journalvoucher.id) AS attach'),DB::RAW('sum(cat.debit_amount) as debit_amount'),DB::RAW('sum(cat.credit_amount) as credit_amount'))
            ->leftjoin('sys_chartofaccounts_transaction as cat','cat.transaction_no','sys_journalvoucher.doc_number')
            ->wherein('sys_journalvoucher.company_id',$company_id)
            ->groupby('sys_journalvoucher.id','sys_journalvoucher.doc_date','sys_journalvoucher.doc_number','sys_journalvoucher.created_by','sys_journalvoucher.narration','sys_journalvoucher.status')->orderby('sys_journalvoucher.doc_date','desc')->get();

            return view('backEnd.journal-voucher.journalvoucheradd', compact('accounts','currency','page_id','company','deal_id','editDataList','vat_account_val','vat_account_text','vat_account_code','journalvoucher','filter_by','ctrl_date','ctrl_date2','documents_number','get_staff_list'));
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function journalvoucherImport(Request $request)
    {
        try {            
            // Decode the incoming JSON data
            $data = json_decode($request->input('data'), true);

            if (empty($data)) {
                return response()->json(['message' => 'No data received.'], 400);
            }

            foreach ($data as $row) {
                // Process each row and insert into the database (or handle accordingly)
                $id = DB::table('sys_chartofaccounts')->select('id')->where('account_code',$row['account_id'])->first();
                if(isset($id)){
                    $id=$id->id;
                } else { $id=0; }
                DB::table('sys_journalvoucher_cart')->insert([
                    'cart_id' => session('logged_session_data.cart_id'),
                    'account_id' => $id,
                    'account_name' => $row['account_name'],
                    'amount_dr' => $row['amount_dr'],
                    'amount_cr' => $row['amount_cr'],
                    'remarks' => $row['remarks'],
                    'company_id' => session('logged_session_data.company_id'),
                    'status' => 1,
                ]);
            }

            return response()->json(['message' => 'Data saved successfully!']);
        } catch (\Throwable $th) {
            return $th;
        }
    }


    public function journalvoucherAdd2($cheque_date)
    {
        try{
            $page_id=null;
            $deal_id=0;
            //$accounts = SysChartofAccounts::select('id','account_name')->wherein('sys_chartofaccounts.company_id',$company_id)->where('status',1)->orderby('account_name','asc')->get();
            $accounts = SysHelper::get_account_list2();
            $currency = SysCurrencySettings::all();
            $get_staff_list = SysHelper::get_sales_persons();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            return view('backEnd.journal-voucher.journalvoucheradd', compact('accounts','currency','page_id','company','deal_id','cheque_date','get_staff_list'));
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function journalvoucherAddDeal(Request $request,$deal_id=null,$cust_id=null)
    {
        try{
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $page_id=0;
            //$accounts = SysChartofAccounts::select('id','account_name')->wherein('sys_chartofaccounts.company_id',$company_id)->where('status',1)->orderby('account_name','asc')->get();
            
            $deal_acc_id = SysHelper::get_account_id_from_cust_id($cust_id);
            $deal_id = SysHelper::get_code_from_dealid($deal_id);
            $accounts = SysHelper::get_account_list2();
            $currency = SysCurrencySettings::all();
            $get_staff_list = SysHelper::get_staff_list();
            $company = SysCompany::find(session('logged_session_data.company_id'));


            $vat_account_val = SysHelper::get_purchase_vat_account_id();
            $vat_account_text = SysChartofAccounts::where('id',$vat_account_val)->value('account_name');
            $editDataList = DB::table('sys_journalvoucher_cart as cart')->select('cart.*')
            ->where(['cart_id' => session('logged_session_data.cart_id'), 'company_id' => session('logged_session_data.company_id'),'status' => 1])
            ->where('account_id','!=',0)->get();
            
            $journalvoucher = SysJournalVoucher::select('sys_journalvoucher.id','sys_journalvoucher.doc_date','sys_journalvoucher.doc_number','sys_journalvoucher.created_by','sys_journalvoucher.narration','sys_journalvoucher.status',DB::RAW('(SELECT GROUP_CONCAT(doc_file) FROM sys_journalvoucher_att WHERE doc_id = sys_journalvoucher.id) AS attach'),DB::RAW('sum(cat.debit_amount) as debit_amount'),DB::RAW('sum(cat.credit_amount) as credit_amount'))
            ->leftjoin('sys_chartofaccounts_transaction as cat','cat.transaction_no','sys_journalvoucher.doc_number')
            ->wherein('sys_journalvoucher.company_id',$company_id)
            ->groupby('sys_journalvoucher.id','sys_journalvoucher.doc_date','sys_journalvoucher.doc_number','sys_journalvoucher.created_by','sys_journalvoucher.narration','sys_journalvoucher.status')->orderby('sys_journalvoucher.doc_date','desc')->get();
            

            $vat_account_code = SysChartofAccounts::where('id',$vat_account_val)->value('account_code');
            $documents_number="";
            $filter_by="";
            $ctrl_date="";
            $ctrl_date2="";

            return view('backEnd.journal-voucher.journalvoucheradd', compact('accounts','currency','page_id','company','deal_id','editDataList','vat_account_val','vat_account_text','journalvoucher','get_staff_list','vat_account_code','filter_by','ctrl_date','ctrl_date2','documents_number'));

        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function getjvaccolist(){
        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];
        $items = SysChartofAccounts::select('id','account_name')->where('status',1)->wherein('company_id',$company_id)->orderby('account_name','asc')->get();
        $searchData = [];
        foreach($items as $item){
            $searchData[] =  ['id' => $item->id, 'name' => $item->account_name];
        }
        if(!empty($searchData)){
            return json_encode($searchData);
        }
    }

    public function search(Request $request)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $q = $request->get('query');
            $formattedDate = null;
            if (preg_match('/\d{2}[\/\-]\d{2}[\/\-]\d{4}/', $q)) {
                $normalized = str_replace('/', '-', $q);
                $formattedDate = date('Y-m-d', strtotime($normalized));
            }

    $invoices = SysJournalVoucher::select('sys_journalvoucher.id', 'sys_journalvoucher.doc_date', 'sys_journalvoucher.doc_number', 'sys_journalvoucher.created_by', 'sys_journalvoucher.narration', 'sys_journalvoucher.status', DB::raw('(SELECT GROUP_CONCAT(doc_file) FROM sys_journalvoucher_att WHERE doc_id = sys_journalvoucher.id) AS attach'), DB::raw('SUM(cat.debit_amount) as debit_amount'), DB::raw('SUM(cat.credit_amount) as credit_amount'), 'sys_currency.code as currency_code'
        )
    ->leftJoin('sys_chartofaccounts_transaction as cat','cat.transaction_no','sys_journalvoucher.doc_number')
    ->join('sys_currency', 'sys_currency.id', '=', 'sys_journalvoucher.currency')
    ->whereIn('sys_journalvoucher.company_id',$company_id)
    ->where(function($query) use ($q, $formattedDate) {
        $query->where('sys_journalvoucher.doc_number', 'like', "%{$q}%")
              ->orWhere('sys_journalvoucher.narration', 'like', "%{$q}%");
        if ($formattedDate) {
            $query->orWhere('sys_journalvoucher.doc_date', 'like', "%{$formattedDate}%");
        }
    })
    ->groupBy(
        'sys_journalvoucher.id', 'sys_journalvoucher.doc_date', 'sys_journalvoucher.doc_number', 'sys_journalvoucher.created_by', 'sys_journalvoucher.narration', 'sys_journalvoucher.status', 'sys_currency.code')
    ->orderBy('sys_journalvoucher.id','desc')
    ->limit(100)
    ->get();
            return response()->json($invoices);            
        } catch (\Throwable $th) {
            return $th;
        }
    }
    
    public function store(Request $request)
    {
        
        //return $request->all();
        for($i = 0; $i < count($request->account_id); $i++) {
            if($request->account_id[$i] !="" && ($request->amount_dr[$i] !="" || $request->amount_cr[$i] !="")){ }
            else if($request->account_id[$i] =="" && $request->amount_dr[$i] =="" && $request->amount_cr[$i] ==""){ }
            else{ Toastr::error('Invalid Entry', 'Failed'); return redirect()->back();}
        }
        if($request->account_id[0] !="" && ($request->amount_dr[0] !="" || $request->amount_cr[0] !="")) { }
        else { Toastr::error('Items not found', 'Failed'); return redirect()->back(); }

$amountDr = array_map(function ($value) {
return $value !== "" ? str_replace(',', '', $value) : $value;
}, $request->amount_dr);
$amountCr = array_map(function ($value) {
return $value !== "" ? str_replace(',', '', $value) : $value;
}, $request->amount_cr);

        $varDr=0;
        $varCr=0;
        for($i = 0; $i < count($amountDr); $i++) {
            if($amountDr[$i] !=""){
                $varDr += $amountDr[$i];
            }
        }
        //return $varDr;
        for($i = 0; $i < count($amountCr); $i++) {
            if($amountCr[$i] !=""){
                $varCr += $amountCr[$i];
            }
        }
        //return $varCr;

        if($varDr==$varCr) {
            
        }
        else{            
            Toastr::error('Debit & Credit Amount are not Equal', 'Failed');
            return redirect()->back();
        }
        
        try{
            DB::beginTransaction();
            $narration = "";
            if(count(array_filter($request->account_id))>0){
                $jv = new SysJournalVoucher();
                $jv->doc_number = SysHelper::get_new_code('sys_journalvoucher','JV','doc_number');
                $jv->doc_date = SysHelper::normalizeToYmd($request->doc_date);
                $jv->currency = $request->currency;
                $jv->narration = $request->narration;
                $jv->status = 1;
                $jv->created_by = Auth::user()->id;
                $jv->created_at = Carbon::now('+04:00');
                $jv->company_id = session('logged_session_data.company_id');
                $jv->deal_id = SysHelper::get_dealid_from_code($request->deal_id);
                $results = $jv->save();
                $jv->toArray();
                
                $entry_no = 1;
                $de = 0;
                $cr = 0;

                for($i = 0; $i < count($request->account_id); $i++) {
                    if($request->account_id[$i] !="" && ($amountDr[$i] !="" || $amountCr[$i] !="")){

                        if($amountDr[$i] != "") { $de +=  $amountDr[$i]; }
                        if($amountCr[$i] != "") { $cr +=  $amountCr[$i]; }
                            
                        $amount_dr = $amountDr[$i] === '' ? '0.00' : $amountDr[$i];
                        $amount_cr = $amountCr[$i] === '' ? '0.00' : $amountCr[$i];

                        //$narration .= $request->remarks[$i] . " | ";
                        

                        $deal_id_code =$request->dealid[$i];
                        

                        SysHelper::trn_chartof_accounts_transaction($request->account_id[$i],$jv->id,$jv->doc_number,$jv->doc_date,'journalvoucher',$amount_dr,$amount_cr,$request->remarks[$i],1,0,$deal_id_code,$entry_no);

                        if($de == $cr){ $entry_no++;}
                    }
                }

                $validate_entry = SysChartofAccountsTransaction::where('transaction_type','journalvoucher')->where('transaction_id',$jv->id)->get();
                $max_num = $validate_entry->max('entry_no');                
                for($i = 1; $i <= $max_num; $i++)
                {
                    $ret_amount=$validate_entry->where('entry_no',$i)->sum('credit_amount');
                    SysChartofAccountsTransaction::select('id')->where('entry_no',$i)->where('transaction_type','journalvoucher')->where('transaction_id',$jv->id)
                    ->where(function($query) use ($ret_amount) {$query->where('debit_amount', $ret_amount)->orwhere('credit_amount',$ret_amount);})
                    ->update(['is_main_account' => 1,]);
                }

                //insert into Receipt Adjustment
                $temp = SysReceiptAdjustmentsTemp::where('process_id',$request->process_id)->get();
                //return $temp;
                if(count($temp)>0){
                    foreach ($temp as $te) {
                        $temp_data[]=[
                            'transaction_type' => $te->transaction_type,
                            'bi_cheque_amount' => $te->bi_cheque_amount,
                            'bi_amount_adjusted' => $te->bi_amount_adjusted,
                            'bi_balance_to_adjust' => $te->bi_balance_to_adjust,
                            'bi_extra_amount' => $te->bi_extra_amount,
                            'bi_currency' => $te->bi_currency,
                            'bi_doc_number' => $jv->doc_number,
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
                    SysReceiptAdjustmentsTemp::where('process_id',$request->process_id)->delete();
                }

                DB::table('sys_receipt_adjustments_jv')->where('cart_id',session('logged_session_data.cart_id'))->where('jv_id','nill')->where('status',3)->where('company_id',session('logged_session_data.company_id'))->update(['jv_id' => $jv->doc_number,'status' => 1]);
                DB::table('sys_payment_adjustments_jv')->where('cart_id',session('logged_session_data.cart_id'))->where('jv_id','nill')->where('status',3)->where('company_id',session('logged_session_data.company_id'))->update(['jv_id' => $jv->doc_number,'status' => 1]);

                //insert into Payment Adjustment
                $temp = SysPaymentAdjustmentsTemp::where('process_id',$request->process_id)->get();
                //return $temp;
                if(count($temp)>0){
                    foreach ($temp as $te) {
                        $temp_data2[]=[
                            'transaction_type' => $te->transaction_type,
                            'bi_cheque_amount' => $te->bi_cheque_amount,
                            'bi_amount_adjusted' => $te->bi_amount_adjusted,
                            'bi_balance_to_adjust' => $te->bi_balance_to_adjust,
                            'bi_extra_amount' => $te->bi_extra_amount,
                            'bi_currency' => $te->bi_currency,
                            'bi_doc_number' => $jv->doc_number,
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
                    SysPaymentAdjustments::insert($temp_data2);
                    SysPaymentAdjustmentsTemp::where('process_id',$request->process_id)->delete();
                }

                DB::table('sys_journalvoucher_att')->where('cart_id',session('logged_session_data.cart_id'))->where('doc_id',0)->where('company_id',session('logged_session_data.company_id'))->update(['doc_id' => $jv->id]);
                //DB::table('sys_journalvoucher')->where('id',$jv->id)->update(['narration' => $narration]);

                
            DB::table('sys_journalvoucher_cart as cart')
            ->where(['cart_id' => session('logged_session_data.cart_id'), 'company_id' => session('logged_session_data.company_id')])->delete();

                DB::commit();
                Toastr::success('Operation successful', 'Success');
                if($request->page_id == "cashbook"){
                    return redirect('cashbook');
                } elseif($request->page_id == "bankbook"){
                    return redirect('bankbook');
                } else{
                    if ($request->deal_page == "1" && !empty($jv->deal_id) && !empty($request->deal_id)) 
                        {
                           $id = SysCrmDealTrack::where('deal_id', $jv->deal_id)->value('id');
                            return redirect('crm-deal-track-approval-list/' . $id);
                        }
                    return redirect('journalvoucher/'.$jv->id);
                }
            }
            else{
                Toastr::error('Operation Failed. please enter valid data', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
            DB::rollback();
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }


public function editData($id)
    {
       try{
        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];
            $documents_number="";
            $filter_by="";
            $ctrl_date="";
            $ctrl_date2="";
        //$accounts = SysHelper::get_account_list_all();
        $accounts = SysHelper::get_account_list2();
        $currency = SysCurrencySettings::all();
        $editData = SysJournalVoucher::find($id);
        $get_staff_list = SysHelper::get_staff_list();
        $company = SysCompany::find(session('logged_session_data.company_id'));
        //$editDataList = SysJournalVoucherList::where('jv_id',$editData->doc_number)->get();
        $editDataList = SysChartofAccountsTransaction::where(['transaction_id' => $id, 'transaction_type' => 'journalvoucher'])->get();
        $editDataAdjustmentsR = SysReceiptAdjustments::where('bi_doc_number', $editData->doc_number)->where('status',1)->get();
        $editDataAdjustmentsP = SysPaymentAdjustments::where('bi_doc_number', $editData->doc_number)->where('status',1)->get();

        
        $journalvoucher = SysJournalVoucher::select('sys_journalvoucher.id','sys_journalvoucher.doc_date','sys_journalvoucher.doc_number','sys_journalvoucher.created_by','sys_journalvoucher.narration','sys_journalvoucher.status',DB::RAW('(SELECT GROUP_CONCAT(doc_file) FROM sys_journalvoucher_att WHERE doc_id = sys_journalvoucher.id) AS attach'),DB::RAW('sum(cat.debit_amount) as debit_amount'),DB::RAW('sum(cat.credit_amount) as credit_amount'))
        ->leftjoin('sys_chartofaccounts_transaction as cat','cat.transaction_no','sys_journalvoucher.doc_number')
        ->wherein('sys_journalvoucher.company_id',$company_id)
        ->groupby('sys_journalvoucher.id','sys_journalvoucher.doc_date','sys_journalvoucher.doc_number','sys_journalvoucher.created_by','sys_journalvoucher.narration','sys_journalvoucher.status')->orderby('sys_journalvoucher.doc_date','desc')->get();
        
      

        return compact('accounts', 'currency', 'editData','editDataList','company','editDataAdjustmentsR','editDataAdjustmentsP','journalvoucher','documents_number','filter_by','ctrl_date','ctrl_date2','get_staff_list');
        }catch (\Exception $e) {
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back(); 
        }
    }

    public function edit(Request $request,$id)
    {
       try{
        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];
            $documents_number="";
            $filter_by="";
            $ctrl_date="";
            $ctrl_date2="";
        //$accounts = SysHelper::get_account_list_all();
        $accounts = SysHelper::get_account_list2();
        $currency = SysCurrencySettings::all();
        $editData = SysJournalVoucher::find($id);
        $get_staff_list = SysHelper::get_staff_list();
        $company = SysCompany::find(session('logged_session_data.company_id'));
        //$editDataList = SysJournalVoucherList::where('jv_id',$editData->doc_number)->get();
        $editDataList = SysChartofAccountsTransaction::where(['transaction_id' => $id, 'transaction_type' => 'journalvoucher'])->get();
        $editDataAdjustmentsR = SysReceiptAdjustments::where('bi_doc_number', $editData->doc_number)->where('status',1)->get();
        $editDataAdjustmentsP = SysPaymentAdjustments::where('bi_doc_number', $editData->doc_number)->where('status',1)->get();

        
        $journalvoucher = SysJournalVoucher::select('sys_journalvoucher.id','sys_journalvoucher.doc_date','sys_journalvoucher.doc_number','sys_journalvoucher.created_by','sys_journalvoucher.narration','sys_journalvoucher.status',DB::RAW('(SELECT GROUP_CONCAT(doc_file) FROM sys_journalvoucher_att WHERE doc_id = sys_journalvoucher.id) AS attach'),DB::RAW('sum(cat.debit_amount) as debit_amount'),DB::RAW('sum(cat.credit_amount) as credit_amount'))
        ->leftjoin('sys_chartofaccounts_transaction as cat','cat.transaction_no','sys_journalvoucher.doc_number')
        ->wherein('sys_journalvoucher.company_id',$company_id)
        ->groupby('sys_journalvoucher.id','sys_journalvoucher.doc_date','sys_journalvoucher.doc_number','sys_journalvoucher.created_by','sys_journalvoucher.narration','sys_journalvoucher.status')->orderby('sys_journalvoucher.doc_date','desc')->get();
        
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
             $data = [];
             $data['editData'] = $editData->toArray();
             $data['editDataList'] = $editDataList->toArray();
             return ApiBaseMethod::sendResponse($data, null);
        }

        return view('backEnd.journal-voucher.journalvoucheredit', compact('accounts', 'currency', 'editData','editDataList','company','editDataAdjustmentsR','editDataAdjustmentsP','journalvoucher','documents_number','filter_by','ctrl_date','ctrl_date2','get_staff_list'));
        }catch (\Exception $e) {
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back(); 
        }
    }
    public function view(Request $request,$id)
    {
        try{
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $accounts = SysHelper::get_account_list_all();
            $currency = SysCurrencySettings::all();
            $editData = SysJournalVoucher::find($id);
            $editDataList = SysJournalVoucherList::where('jv_id',$editData->doc_number)->get();
            $editDataList = SysChartofAccountsTransaction::where(['transaction_id' => $id, 'transaction_type' => 'journalvoucher'])->get();
            $editDataAdjustmentsR = SysReceiptAdjustments::where('bi_doc_number', $editData->doc_number)->where('status',1)->get();
            $editDataAdjustmentsP = SysPaymentAdjustments::where('bi_doc_number', $editData->doc_number)->where('status',1)->get();
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['editData'] = $editData->toArray();
                $data['editDataList'] = $editDataList->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.journal-voucher.journalvoucherview', compact('accounts', 'currency', 'editData','editDataList','editDataAdjustmentsR','editDataAdjustmentsP'));
            }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
         }
    }

    public function update(Request $request, $id)
    {
        if($request->account_id[0] !="" && ($request->amount_dr[0] !="" || $request->amount_cr[0] !=""))
        {
            
        }
        else
        {
            Toastr::error('Items not found', 'Failed');
            return redirect()->back();
        }
$amountDr = array_map(function ($value) {
return $value !== "" ? str_replace(',', '', $value) : $value;
}, $request->amount_dr);
$amountCr = array_map(function ($value) {
return $value !== "" ? str_replace(',', '', $value) : $value;
}, $request->amount_cr);

        $varDr=0;
        $varCr=0;
        for($i = 0; $i < count($amountDr); $i++) {
            if($amountDr[$i] !=""){
                $varDr += $amountDr[$i];
            }
        }
        //return $varDr;
        for($i = 0; $i < count($amountCr); $i++) {
            if($amountCr[$i] !=""){
                $varCr += $amountCr[$i];
            }
        }

        if($varDr==$varCr) {
            
        }
        else{            
            Toastr::error('Debit & Credit Amount are not Equal', 'Failed');
            return redirect()->back();
        }
        
        try {
            DB::beginTransaction();
            $narration = "";
            $jv = SysJournalVoucher::find($id);
            $jv->doc_date = SysHelper::normalizeToYmd($request->doc_date);
            $jv->currency = $request->currency;
            $jv->narration = $request->narration;
            $jv->status = 1;
            $jv->updated_by = Auth::user()->id;
            $jv->updated_at = Carbon::now('+04:00');
            $jv->deal_id = $request->deal_id;
            //$jv->company_id = session('logged_session_data.company_id');
            $jv->update();
            
            $entry_no = 1;
            $de = 0;
            $cr = 0;

            /*$array = array_merge($request->amount_cr,$request->amount_dr);
            $maxIndex = array_search(max($array), $array);
            return $array[$maxIndex];*/

            SysChartofAccountsTransaction::query()
                ->where(['transaction_id' => $id, 'transaction_type' => 'journalvoucher'])
                ->each(function ($oldRecord) {
                $newRecord = $oldRecord->replicate();
                $newRecord->setTable('sys_chartofaccounts_transaction_history');
                $newRecord->save();
                $oldRecord->delete();
                });

                for($i = 0; $i < count($request->account_id); $i++) {
                    if($request->account_id[$i] !="" && ($amountDr[$i] !="" || $amountCr[$i] !="")){
                            
                    if($amountDr[$i] != "") { $de +=  $amountDr[$i]; }
                    if($amountCr[$i] != "") { $cr +=  $amountCr[$i]; }

                    $amount_dr = $amountDr[$i] === '' ? '0.00' : $amountDr[$i];
                    $amount_cr = $amountCr[$i] === '' ? '0.00' : $amountCr[$i];
                    
                    $deal_id_code = $request->dealid[$i];

                    

                    SysHelper::trn_chartof_accounts_transaction($request->account_id[$i],$jv->id,$jv->doc_number,$jv->doc_date,'journalvoucher',$amount_dr,$amount_cr,$request->remarks[$i],1,0,$deal_id_code,$entry_no);
                    
                    if($de == $cr){ $entry_no++;}

                    }


                }

                $validate_entry = SysChartofAccountsTransaction::where('transaction_type','journalvoucher')->where('transaction_id',$jv->id)->get();
                $max_num = $validate_entry->max('entry_no');                
                for($i = 1; $i <= $max_num; $i++)
                {
                    $ret_amount=$validate_entry->where('entry_no',$i)->sum('credit_amount');
                    SysChartofAccountsTransaction::select('id')->where('entry_no',$i)->where('transaction_type','journalvoucher')->where('transaction_id',$jv->id)
                    ->where(function($query) use ($ret_amount) {$query->where('debit_amount', $ret_amount)->orwhere('credit_amount',$ret_amount);})
                    ->update(['is_main_account' => 1,]);
                }

            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect('journalvoucher/'.$id);
            
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function delete($id){
        try{
            DB::beginTransaction();
            $jv_doc_number = DB::table('sys_journalvoucher')->where('id',$id)->max('doc_number');
            DB::table('sys_journalvoucher')->where('id',$id)->update(['status' => 2]);
            DB::table('sys_chartofaccounts_transaction')->where('transaction_id',$id)->where('transaction_type','journalvoucher')->update(['status' => 2]);
            DB::table('sys_receipt_adjustments')->where('bi_doc_number',$jv_doc_number)->delete();
            DB::table('sys_payment_adjustments')->where('bi_doc_number',$jv_doc_number)->delete();
            db::commit();
            Toastr::success('Deleted successfully', 'Success');
            return redirect()->back(); 
        }catch (\Exception $e) {
            db::rollBack();
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }
    public function restore($id){
        try{
            DB::beginTransaction();
            $jv_doc_number = DB::table('sys_journalvoucher')->where('id',$id)->max('doc_number');
            DB::table('sys_journalvoucher')->where('id',$id)->update(['status' => 1]);
            DB::table('sys_chartofaccounts_transaction')->where('transaction_id',$id)->where('transaction_type','journalvoucher')->update(['status' => 1]);
            //DB::table('sys_receipt_adjustments')->where('bi_doc_number',$jv_doc_number)->update(['status' => 1]);
            //DB::table('sys_payment_adjustments')->where('bi_doc_number',$jv_doc_number)->update(['status' => 1]);
            db::commit();
            Toastr::success('Activated successfully', 'Success');
            return redirect()->back(); 
        }catch (\Exception $e) {
            db::rollBack();
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }

    public function journalvoucher_item_delete(Request $request){
        try{
            SysChartofAccountsTransaction::where(['id' => $request->id, 'transaction_type' => 'journalvoucher'])->delete();
            SysReceiptAdjustments::where(['bi_doc_number' => $request->doc_number, 'account_id' => $request->account_id])->delete();
            SysPaymentAdjustments::where(['bi_doc_number' => $request->doc_number, 'account_id' => $request->account_id])->delete();            
            $ret = 'SUCCESS';
            return json_encode(array('data'=>$ret));

        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }
    

    public function get_adjestment_list(Request $request)
    {
        $company_id = session('logged_session_data.company_id');

        if($request->account_type==""){}
        $opb = SysChartofAccountsTransaction::wherein('transaction_type',['openingbalance11111','opbinvoice'])->where('account_id',$request->account_id)->where('company_id',$company_id)->get();
        $items = DB::select("CALL get_bank_payment_adjestments($request->account_id,$company_id)");
        
		$searchData = [];
        
        if(count($opb)>0){
            foreach($opb as $dt){
               $paid = SysPaymentAdjustments::where('bi_doc_no',$dt->transaction_no)->sum('bi_paid');
               $searchData[] =  [
                   'doc_number' => $dt->transaction_no,
                   'doc_date' => $dt->transaction_date,
                   'lpo_number' => '',
                   'lpo_date' => '',
                   'total' => abs($dt->debit_amount - $dt->credit_amount),
                   'paid' => $paid,
                   'balance' => abs($dt->debit_amount - $dt->credit_amount)-$paid,
               ];
            }
        }

		foreach($items as $item){
			$searchData[] =  [
                'doc_number' => $item->doc_number,
                'doc_date' => $item->doc_date,
                'lpo_number' => $item->lpo_number,
                'lpo_date' => $item->lpo_date,
                'total' => $item->total,
                'paid' => $item->paid,
                'balance' => $item->balance,
            ];
		}
        
		if(!empty($searchData)){
			return json_encode($searchData);
        }

        // ------------

        $opb2 = SysChartofAccountsTransaction::wherein('transaction_type',['openingbalance11111','opbinvoice'])->where('account_id',$request->account_id)->where('company_id',$company_id)->get();
        $items2 = DB::select("CALL get_bank_receipt_adjestments($request->account_id,$company_id)");
		$searchData2 = [];

        if(count($opb2)>0){
         foreach($opb2 as $dt){
            $paid2 = SysReceiptAdjustments::where('bi_doc_no',$dt->transaction_no)->sum('bi_paid');
            $searchData[] =  [
                'doc_number' => $dt->transaction_no,
                'doc_date' => $dt->transaction_date,
                'lpo_number' => '',
                'lpo_date' => '',
                'total' => abs($dt->debit_amount - $dt->credit_amount),
                'paid' => $paid2,
                'balance' => abs($dt->debit_amount - $dt->credit_amount)-$paid2,
            ];
         }
        }
		foreach($items2 as $item){
			$searchData[] =  [
                'doc_number' => $item->doc_number,
                'doc_date' => $item->doc_date,
                'lpo_number' => $item->lpo_number,
                'lpo_date' => $item->lpo_date,
                'total' => $item->total,
                'paid' => $item->paid,
                'balance' => $item->balance,
            ];
		}
		if(!empty($searchData)){
			return json_encode($searchData);
        }
    }
    public function get_adjestment_list_cus(Request $request)
    {
        try {
            $company_id = session('logged_session_data.company_id');
            if($request->account_type==""){}
            
            $opb2 = SysChartofAccountsTransaction::wherein('transaction_type',['openingbalance11111','opbinvoice'])->where('account_id',$request->account_id)->where('company_id',$company_id)->get();
            $items2 = DB::select("CALL get_bank_receipt_adjestments($request->account_id,$company_id)");
            $searchData2 = [];

            if(count($opb2)>0){
            foreach($opb2 as $dt){
                $paid2 = SysReceiptAdjustments::where('bi_doc_no',$dt->transaction_no)->sum('bi_paid');
                $searchData[] =  [
                    'doc_number' => $dt->transaction_no,
                    'doc_date' => $dt->transaction_date,
                    'lpo_number' => '',
                    'lpo_date' => '',
                    'total' => abs($dt->debit_amount - $dt->credit_amount),
                    'paid' => $paid2,
                    'balance' => abs($dt->debit_amount - $dt->credit_amount)-$paid2,
                ];
            }
            }
            foreach($items2 as $item){
                $searchData[] =  [
                    'doc_number' => $item->doc_number,
                    'doc_date' => $item->doc_date,
                    'lpo_number' => $item->lpo_number,
                    'lpo_date' => $item->lpo_date,
                    'total' => $item->total,
                    'paid' => $item->paid,
                    'balance' => $item->balance,
                ];
            }

            if(!empty($searchData)){
                return json_encode($searchData);
            }
        } catch (\Throwable $th) {
            return json_encode($th);
        }
    }
    public function get_adjestment_list_sup(Request $request)
    {
        try {
            $company_id = session('logged_session_data.company_id');

            if($request->account_type==""){}
            $opb = SysChartofAccountsTransaction::wherein('transaction_type',['openingbalance11111','opbinvoice'])->where('account_id',$request->account_id)->where('company_id',$company_id)->get();
            $items = DB::select("CALL get_bank_payment_adjestments($request->account_id,$company_id)");
            
            $searchData = [];
            
            if(count($opb)>0){
                foreach($opb as $dt){
                $paid = SysPaymentAdjustments::where('bi_doc_no',$dt->transaction_no)->sum('bi_paid');
                $searchData[] =  [
                    'doc_number' => $dt->transaction_no,
                    'doc_date' => $dt->transaction_date,
                    'lpo_number' => '',
                    'lpo_date' => '',
                    'total' => abs($dt->debit_amount - $dt->credit_amount),
                    'paid' => $paid,
                    'balance' => abs($dt->debit_amount - $dt->credit_amount)-$paid,
                ];
                }
            }

            foreach($items as $item){
                $searchData[] =  [
                    'doc_number' => $item->doc_number,
                    'doc_date' => $item->doc_date,
                    'lpo_number' => $item->lpo_number,
                    'lpo_date' => $item->lpo_date,
                    'total' => $item->total,
                    'paid' => $item->paid,
                    'balance' => $item->balance,
                ];
            }
            
            if(!empty($searchData)){
                return json_encode($searchData);
            }
        } catch (\Throwable $th) {
            return json_encode($th);
        }
    }

    public function get_adjestment_list_edit(Request $request)
    {
        $company_id = session('logged_session_data.company_id');
        $opb = SysChartofAccountsTransaction::wherein('transaction_type',['openingbalance11111','opbinvoice'])->where('account_id',$request->account_id)->where('company_id',$company_id)->get();
        $items = DB::select("CALL get_bank_receipt_adjestments_edit($request->account_id,$company_id)");

		$searchData = [];

        $adjestData = SysReceiptAdjustments::where('bi_doc_number',$request->doc_number)->where('account_id',$request->account_id)->get();

        if(count($opb)>0){
         foreach($opb as $dt){
            $paid = SysReceiptAdjustments::where('bi_doc_no',$dt->transaction_no)->where('account_id',$request->account_id)->sum('bi_paid');
            $bi_amount = $adjestData->where('bi_doc_no',$dt->transaction_no)->sum('bi_paid');
            if($bi_amount != 0) { $paid=0; }
            $searchData[] =  [
                'doc_number' => $dt->transaction_no,
                'doc_date' => $dt->transaction_date,
                'lpo_number' => '',
                'lpo_date' => '',
                'total' => abs($dt->debit_amount - $dt->credit_amount),
                'paid' => $paid,
                'bi_amount' => $bi_amount,
                'balance' => abs($dt->debit_amount - $dt->credit_amount)-$paid,
            ];
         }
        }

		foreach($items as $item){
            $paid = $item->paid;
            $bi_amount = $adjestData->where('bi_doc_no',$item->doc_number)->sum('bi_paid');
            if($bi_amount != 0) { $paid=0; }
            if($item->balance>0){
                $searchData[] =  [
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

		if(!empty($searchData)){
			return json_encode($searchData);
        }
    }

    public function get_adjestment_list_edit_cus(Request $request)
    {
        $company_id = session('logged_session_data.company_id');
        $opb = SysChartofAccountsTransaction::wherein('transaction_type',['openingbalance11111','opbinvoice_exe'])->where('account_id',$request->account_id)->where('company_id',$company_id)->get();
        $items = DB::select("CALL get_bank_receipt_adjestments_edit_jv($request->account_id,$company_id,'$request->doc_number')");

		$searchData = [];

        $adjestData = SysReceiptAdjustments::where('bi_doc_number',$request->doc_number)->where('account_id',$request->account_id)->get();

        if(count($opb)>0){
         foreach($opb as $dt){
            $paid = SysReceiptAdjustments::where('bi_doc_no',$dt->transaction_no)->where('account_id',$request->account_id)->sum('bi_paid');
            $bi_amount = $adjestData->where('bi_doc_no',$dt->transaction_no)->sum('bi_paid');
            if($bi_amount != 0) { $paid=0; }
            $balance = abs($dt->debit_amount - $dt->credit_amount)-$paid;
                if($balance > 0){
                    $searchData[] =  [
                        'doc_number' => $dt->transaction_no,
                        'doc_date' => $dt->transaction_date,
                        'lpo_number' => '',
                        'lpo_date' => '',
                        'total' => abs($dt->debit_amount - $dt->credit_amount),
                        'paid' => $paid,
                        'bi_amount' => $bi_amount,
                        'balance' => abs($dt->debit_amount - $dt->credit_amount)-$paid,
                    ];
                }
            }
        }
        
		foreach($items as $item){
            $paid = $item->paid; $add=0;
            $bi_amount = $adjestData->where('bi_doc_no',$item->doc_number)->sum('bi_paid');
            if($bi_amount != 0) { $paid=0; }
            if($item->balance > 0){
                $searchData[] =  [
                    'doc_number' => $item->doc_number,
                    'doc_date' => $item->doc_date,
                    'lpo_number' => $item->lpo_number,
                    'lpo_date' => $item->lpo_date,
                    'total' => $item->total,
                    'paid' => $paid,
                    'bi_amount' => $bi_amount,
                    'balance' => $item->balance,
                ];
                $add=1;
            }
            if($bi_amount > 0 && $add==0){
                $searchData[] =  [
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

		if(!empty($searchData)){
			return json_encode($searchData);
        }
    }

    public function get_adjestment_list_edit_sup(Request $request)
    {
        $company_id = session('logged_session_data.company_id');
        $opb = SysChartofAccountsTransaction::wherein('transaction_type',['openingbalance11111','opbinvoice'])->where('account_id',$request->account_id)->where('company_id',$company_id)->get();
        $items = DB::select("CALL get_bank_payment_adjestments_edit_jv($request->account_id,$company_id,'$request->doc_number')");

		$searchData = [];

        $adjestData = SysPaymentAdjustments::where('bi_doc_number',$request->doc_number)->where('account_id',$request->account_id)->get();

        if(count($opb)>0){
         foreach($opb as $dt){
            $paid = SysPaymentAdjustments::where('bi_doc_no',$dt->transaction_no)->where('account_id',$request->account_id)->sum('bi_paid');
            $bi_amount = $adjestData->where('bi_doc_no',$dt->transaction_no)->sum('bi_paid');
            if($bi_amount != 0) { $paid=0; }
            $balance = abs($dt->debit_amount - $dt->credit_amount)-$paid;
                if($balance > 0){
                    $searchData[] =  [
                        'doc_number' => $dt->transaction_no,
                        'doc_date' => $dt->transaction_date,
                        'lpo_number' => '',
                        'lpo_date' => '',
                        'total' => abs($dt->debit_amount - $dt->credit_amount),
                        'paid' => $paid,
                        'bi_amount' => $bi_amount,
                        'balance' => abs($dt->debit_amount - $dt->credit_amount)-$paid,
                    ];
                }
            }
        }
        
		foreach($items as $item){
            $paid = $item->paid; $add=0;
            $bi_amount = $adjestData->where('bi_doc_no',$item->doc_number)->sum('bi_paid');
            if($bi_amount != 0) { $paid=0; }
            if($item->balance > 0){
                $searchData[] =  [
                    'doc_number' => $item->doc_number,
                    'doc_date' => $item->doc_date,
                    'lpo_number' => $item->lpo_number,
                    'lpo_date' => $item->lpo_date,
                    'total' => $item->total,
                    'paid' => $paid,
                    'bi_amount' => $bi_amount,
                    'balance' => $item->balance,
                ];
                $add=1;
            }
            if($bi_amount > 0 && $add==0){
                $searchData[] =  [
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

		if(!empty($searchData)){
			return json_encode($searchData);
        }
    }

    public function journalvoucher_get_adjestment_update(Request $request){
        try {

            //return $request->add_url;

            DB::beginTransaction();
            $transaction_type2 = $request->transaction_type2;
            if($request->add_url=="journalvoucher-get-adjestment-list-edit-cus"){
                $transaction_type2 = 'journalreceipt';
                SysReceiptAdjustments::where('bi_doc_number',$request->doc_number2)->where('account_id',$request->br_account_id)->delete();
                
                $transaction = SysChartofAccountsTransaction::where('transaction_no',$request->doc_number2)->get();
                if($transaction->where('account_id',$request->br_account_id)->count()==0){                    
                    SysChartofAccountsTransaction::where('account_id',$request->br_account_id)->where('transaction_no',$request->doc_number2)->delete();

                    SysHelper::trn_chartof_accounts_transaction_with_main($request->br_account_id,$transaction[0]->transaction_id,$request->doc_number2,$transaction[0]->transaction_date,'journalreceipt','0.00',$request->br_account_id_amount,'',1,0,"",1,0);
                }
                

                $amount = SysChartofAccountsTransaction::where(['transaction_no' => $request->doc_number2])->sum('credit_amount');
                SysChartofAccountsTransaction::where(['transaction_no' => $request->doc_number2])
                ->where(['is_main_account' => 1])->where(['credit_amount' => '0.00'])->update(['debit_amount' => $amount]);

            }
            if($request->add_url=="journalvoucher-get-adjestment-list-edit-sup"){
                $transaction_type2 = 'journalpayment';
                SysPaymentAdjustments::where('bi_doc_number',$request->doc_number2)->where('account_id',$request->br_account_id)->delete();
    
                $transaction = SysChartofAccountsTransaction::where('transaction_no',$request->doc_number2)->get();
                if($transaction->where('account_id',$request->br_account_id)->count()==0){
                    SysChartofAccountsTransaction::where('account_id',$request->br_account_id)->where('transaction_no',$request->doc_number2)->delete();

                    SysHelper::trn_chartof_accounts_transaction_with_main($request->br_account_id,$transaction[0]->transaction_id,$request->doc_number2,$transaction[0]->transaction_date,'journalpayment',$request->br_account_id_amount,'0.00','',1,0,"",1,0);
                }
                
                $amount = SysChartofAccountsTransaction::where(['transaction_no' => $request->doc_number2])->sum('debit_amount');
                SysChartofAccountsTransaction::where(['transaction_no' => $request->doc_number2])
                ->where(['is_main_account' => 1])->where(['debit_amount' => '0.00'])->update(['credit_amount' => $amount]);
            }
            

            $temp_data=[];
            for ($i=0; $i < count($request->bi_amount); $i++) {
                if($request->bi_amount[$i] != 0){
                    $temp_data[]=[
                        'transaction_type' => $transaction_type2,
                        'bi_cheque_amount' => $request->bi_cheque_amount,
                        'bi_amount_adjusted' => $request->bi_amount_adjusted,
                        'bi_balance_to_adjust' => $request->bi_balance_to_adjust,
                        'bi_extra_amount' => $request->bi_extra_amount,
                        'bi_currency' => $request->bi_currency2,
                        'bi_doc_number' => $request->doc_number2,
                        'bi_contains' => '',
                        'bi_doc_no' => $request->bi_doc_no[$i],
                        'bi_lpo_no' => $request->bi_lpo_no[$i],
                        'bi_doc_date' => $request->bi_doc_date[$i],
                        'bi_total' => $request->bi_total[$i],
                        'bi_paid' => $request->bi_amount[$i],
                        'bi_balance' => $request->bi_balance[$i],
                        'bi_amount' => $request->bi_amount[$i],
                        'bi_narration' => $request->bi_narration[$i],
                        'account_id' => $request->br_account_id,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                        'company_id' => session('logged_session_data.company_id'),
                    ];
                }
            }
            if($request->add_url=="journalvoucher-get-adjestment-list-edit-cus"){
                if(count($temp_data)>0){ SysReceiptAdjustments::insert($temp_data); }
            }
            if($request->add_url=="journalvoucher-get-adjestment-list-edit-sup"){
                if(count($temp_data)>0){ SysPaymentAdjustments::insert($temp_data); }
            }
            
            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back(); 
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
            //return $th;
        }
    }

    //JV Receipt Start
    public function journalvoucher_get_receipt_adjestment_jv(Request $request)
    {
        try{
            $company_id = session('logged_session_data.company_id');            
            
            $list_of_unadjusted = SysHelper::get_list_of_unadjusted([$request->id],$company_id);
            
            $ret = $list_of_unadjusted;
            return json_encode(array('data'=>$ret));

        }catch (\Exception $e) {
            return $e;
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }
    public function journalvoucher_add_receipt_adjestment_jv(Request $request)
    {
        try{
            db::beginTransaction();
             $set_amt = $request->input('set_amt');
             $receiptno = $request->input('receiptno');
             $set_amt_act = $request->input('set_amt_act');

            DB::table('sys_receipt_adjustments_jv')->where(['account_id' => $request->account_id,'company_id' => session('logged_session_data.company_id'),'cart_id' => session('logged_session_data.cart_id'),'status' => 3,'jv_id' => "nill"])->delete();

            for($i=0; $i < count($set_amt); $i++) {
                if($set_amt[$i] != 0){
                    $data[] = [
                        'account_id' => $request->account_id,
                        'account_amount' => $request->account_amount,
                        'jv_id' => "nill",
                        'receipt_no' => $receiptno[$i],
                        'amount' => $set_amt[$i], 
                        'status' => 3,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                        'company_id' => session('logged_session_data.company_id'),
                        'cart_id' => session('logged_session_data.cart_id'),
                    ];
                }
            }
            if(count($data)>0){
                DB::table('sys_receipt_adjustments_jv')->insert($data);
            }
            db::commit();
            $ret = 'SUCCESS';
            return json_encode($ret);
        }catch (\Exception $e) {
            db::rollBack();
            $ret = $e;
            return json_encode($ret);
        }
    }
    public function journalvoucher_update_receipt_adjestment_jv(Request $request)
    {
        try{
            db::beginTransaction();
             $set_amt = $request->input('set_amt');
             $receiptno = $request->input('receiptno');
             $set_amt_act = $request->input('set_amt_act');

            DB::table('sys_receipt_adjustments_jv')->where(['account_id' => $request->account_id,'company_id' => session('logged_session_data.company_id'),'jv_id' => $request->jv_id])->delete();

            for($i=0; $i < count($set_amt); $i++) {
                if($set_amt[$i] != 0){
                    $data[] = [
                        'account_id' => $request->account_id,
                        'account_amount' => $request->account_amount,
                        'jv_id' => $request->jv_id,
                        'receipt_no' => $receiptno[$i],
                        'amount' => $set_amt[$i], 
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                        'company_id' => session('logged_session_data.company_id'),
                        'cart_id' => session('logged_session_data.cart_id'),
                    ];
                }
            }
            if(count($data)>0){
                DB::table('sys_receipt_adjustments_jv')->insert($data);
            }
            db::commit();
            $ret = 'SUCCESS';
            return json_encode($ret);
        }catch (\Exception $e) {
            db::rollBack();
            $ret = $e;
            return json_encode($ret);
        }
    }    
    public function journalvoucher_get_receipt_adjestment_jv_edit(Request $request)
    {
        try{
            $company_id = session('logged_session_data.company_id');
            $list_of_unadjusted = SysHelper::get_list_of_unadjusted_include_removed_jv([$request->id],$company_id);            
            $ret = $list_of_unadjusted;
            return json_encode(array('data'=>$ret));

        }catch (\Exception $e) {
            return $e;
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }
    //JV Receipt End
    
    //JV Payment Start
    public function journalvoucher_get_payment_adjestment_jv(Request $request)
    {
        try{
            $company_id = session('logged_session_data.company_id');            
            
            $list_of_unadjusted = SysHelper::get_list_of_payable_unadjusted([$request->id],$company_id);
            
            $ret = $list_of_unadjusted;
            return json_encode(array('data'=>$ret));

        }catch (\Exception $e) {
            return $e;
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }
    public function journalvoucher_add_payment_adjestment_jv(Request $request)
    {
        try{
            db::beginTransaction();
             $set_amt = $request->input('set_amt');
             $paymentno = $request->input('paymentno');
             $set_amt_act = $request->input('set_amt_act');

            DB::table('sys_payment_adjustments_jv')->where(['account_id' => $request->account_id,'company_id' => session('logged_session_data.company_id'),'cart_id' => session('logged_session_data.cart_id'),'status' => 3,'jv_id' => "nill"])->delete();

            for($i=0; $i < count($set_amt); $i++) {
                if($set_amt[$i] != 0){
                    $data[] = [
                        'account_id' => $request->account_id,
                        'account_amount' => $request->account_amount,
                        'jv_id' => "nill",
                        'payment_no' => $paymentno[$i],
                        'amount' => $set_amt[$i], 
                        'status' => 3,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                        'company_id' => session('logged_session_data.company_id'),
                        'cart_id' => session('logged_session_data.cart_id'),
                    ];
                }
            }
            if(count($data)>0){
                DB::table('sys_payment_adjustments_jv')->insert($data);
            }
            db::commit();
            $ret = 'SUCCESS';
            return json_encode($ret);
        }catch (\Exception $e) {
            db::rollBack();
            $ret = $e;
            return json_encode($ret);
        }
    }
    public function journalvoucher_update_payment_adjestment_jv(Request $request)
    {
        try{
            db::beginTransaction();
             $set_amt = $request->input('set_amt');
             $paymentno = $request->input('paymentno');
             $set_amt_act = $request->input('set_amt_act');

            DB::table('sys_payment_adjustments_jv')->where(['account_id' => $request->account_id,'company_id' => session('logged_session_data.company_id'),'jv_id' => $request->jv_id])->delete();

            for($i=0; $i < count($set_amt); $i++) {
                if($set_amt[$i] != 0){
                    $data[] = [
                        'account_id' => $request->account_id,
                        'account_amount' => $request->account_amount,
                        'jv_id' => $request->jv_id,
                        'payment_no' => $paymentno[$i],
                        'amount' => $set_amt[$i], 
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                        'company_id' => session('logged_session_data.company_id'),
                        'cart_id' => session('logged_session_data.cart_id'),
                    ];
                }
            }
            if(count($data)>0){
                DB::table('sys_payment_adjustments_jv')->insert($data);
            }
            db::commit();
            $ret = 'SUCCESS';
            return json_encode($ret);
        }catch (\Exception $e) {
            db::rollBack();
            $ret = $e;
            return json_encode($ret);
        }
    }    
    public function journalvoucher_get_payment_adjestment_jv_edit(Request $request)
    {
        try{
            $company_id = session('logged_session_data.company_id');
            $list_of_unadjusted = SysHelper::get_list_of_payable_unadjusted_include_removed_jv([$request->id],$company_id);
            $ret = $list_of_unadjusted;
            return json_encode(array('data'=>$ret));

        }catch (\Exception $e) {
            return $e;
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }
    //JV Payment End




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

    function add_attachment(Request $request)
    {
        try{
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

            if($request->doc_id== 'undefined' || $request->doc_id=='' ){
                $request->doc_id=0;
            }

                $data[] = [
                    'cart_id' => session('logged_session_data.cart_id'),
                    'doc_id' => $request->doc_id ?? null,
                    'doc_file' => $selected_file,
                    'doc_date' => SysHelper::normalizeToYmd($request->att_date),
                    'doc_name' => $request->doc_name,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                    'company_id' => session('logged_session_data.company_id'),
                ];

            DB::table('sys_journalvoucher_att')->insert($data);
            
            
            if($request->doc_id==0){
                $ret = DB::table('sys_journalvoucher_att')->where('doc_id',$request->doc_id)->where('cart_id',session('logged_session_data.cart_id'))->where('company_id',session('logged_session_data.company_id'))->get();
            } else {
                $ret = DB::table('sys_journalvoucher_att')->where('doc_id',$request->doc_id)->where('company_id',session('logged_session_data.company_id'))->get();
            }
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }
        }catch (\Exception $e) {
            //$ret = 'ERROR';
            $ret = $e;
            return json_encode(array('data'=>$ret));
        }
    }
    function view_attachment(Request $request)
    {
        try{

            
            if($request->doc_id== 'undefined' || $request->doc_id=='' ){
                $request->doc_id=0;
            }
            
            if($request->doc_id==0){
                $ret = DB::table('sys_journalvoucher_att')->where('doc_id',$request->doc_id)->where('cart_id',session('logged_session_data.cart_id'))->where('company_id',session('logged_session_data.company_id'))->get();
            } else {
                $ret = DB::table('sys_journalvoucher_att')->where('doc_id',$request->doc_id)->where('company_id',session('logged_session_data.company_id'))->get();
            }
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }
        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }
    function delete_attachment(Request $request)
    {
        try{
            DB::table('sys_journalvoucher_att')->where('id',$request->id)->delete();
            
            if($request->doc_id==0){
                $ret = DB::table('sys_journalvoucher_att')->where('doc_id',$request->doc_id)->where('cart_id',session('logged_session_data.cart_id'))->where('company_id',session('logged_session_data.company_id'))->get();
            } else {
                $ret = DB::table('sys_journalvoucher_att')->where('doc_id',$request->doc_id)->where('company_id',session('logged_session_data.company_id'))->get();
            }
            
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }
        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }
}
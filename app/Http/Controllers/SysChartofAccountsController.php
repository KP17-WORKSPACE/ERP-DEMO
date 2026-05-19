<?php

namespace App\Http\Controllers;

use App\SysChartofAccounts;
use App\SysAccountType;
use App\ApiBaseMethod;
use App\Helper;
use App\Role;
use App\SmStaff;
use App\SmSupplier;
use App\SysAccountGroup;
use App\SysAccountGroupSub;
use App\SysAccountGroupSub2;
use App\SysChartofAccountsImport;
use App\SysChartofAccountsImportSub;
use App\SysChartofaccountsOpeningBalanceInvoice;
use App\SysChartofaccountsOpeningBalanceInvoiceImport;
use App\SysChartofAccountsTransaction;
use App\SysCountries;
use App\SysCountryCode;
use App\SysCustSuppl;
use App\SysHelper;
use App\SysItemStock;
use App\SysPaymentTerms;
use App\SysReceiptAdjustments;
use App\SysSalesReturnAdjestment;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Else_;
use PHPExcel;
use PHPExcel_IOFactory;
use Illuminate\Support\Str;
use App\SysCompanyBanking;

class SysChartofAccountsController extends Controller
{
    private function getCompanyOpeningBalanceDateDmy()
    {
        $companyDate = @optional(
            \App\SysCompany::select('opening_balance_date')
                ->where('id', session('logged_session_data.company_id'))
                ->first()
        )->opening_balance_date;

        if (!empty($companyDate)) {
            return Carbon::parse($companyDate)->format('d/m/Y');
        }

        return Carbon::now()->format('d/m/Y');
    }

    public function __construct()
    {
        $this->middleware('PM');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function chartofaccountsList(Request $request)
    {
        try {
            $account_search = "";
            if ($_POST) {
                $account_search = $request->account_search;
            }
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $com_id = session('logged_session_data.company_id');

            // direct queries without caching
            $accountgroup = SysAccountGroup::where('status', 1)->get();

            $account_sub = SysChartofAccounts::select('id', 'account_code', 'account_name', 'main_account_id', 'group', 'subgroup', 'subgroup2', 'status')
                ->where('main_account_id', '!=', 0)
                ->whereRaw("find_in_set(?, company_access)", [$com_id])
                ->orderby('account_name', 'asc')
                ->get();

            return view('backEnd.chart-of-accounts.chartofaccountslist', compact('accountgroup', 'company_id', 'com_id', 'account_sub', 'account_search'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function search(Request $request)
    {
        $com_id = session('logged_session_data.company_id');
        $q = $request->get('q');

        $results = SysChartofAccounts::with(['mainaccount:id,account_name', 'subgroupname:id,title', 'subgroup2name:id,title', 'groupname:id,title'])
            ->select('id', 'account_code', 'account_name', 'main_account_id', 'group', 'subgroup', 'subgroup2', 'status')
            ->whereRaw("find_in_set(?, company_access)", [$com_id])
            ->where(function ($query) use ($q) {
                $query->where('account_name', 'like', "%{$q}%")
                    ->orWhere('account_code', 'like', "%{$q}%");
            })
            ->take(50)
            ->get();

        return view('backEnd.chart-of-accounts.chartofaccount_search', compact('results'));
    }

    //   public function search(Request $request)
    // {
    //     $com_id = session('logged_session_data.company_id');
    //     $q = $request->get('q');

    //     // Add simple caching for search results (2 minutes)
    //     $cacheKey = "chart_search_{$com_id}_" . md5($q);
    //     $results = cache()->remember($cacheKey, 120, function () use ($com_id, $q) {
    //         return SysChartofAccounts::with(['mainaccount:id,account_name','subgroupname:id,title','subgroup2name:id,title','groupname:id,title'])->select('id', 'account_code', 'account_name', 'main_account_id', 'group', 'subgroup', 'subgroup2', 'status')
    //                     ->whereRaw("find_in_set(?, company_access)", [$com_id])
    //                     ->where(function($query) use ($q) {
    //                         $query->where('account_name', 'like', "%{$q}%")
    //                             ->orWhere('account_code', 'like', "%{$q}%");
    //                     })
    //                     ->take(50) // Reduce from 100 to 50 for better performance
    //                     ->get();
    //     });


    //     return view('backEnd.chart-of-accounts.chartofaccount_search', compact('results'));
    // }

    public function chartofaccountsAdd(Request $request)
    {
        try {
            //$telcode = SysCountries::All();
            //$accountgroup = SysAccountGroup::where('status',1)->get();
            //$accounttype = SysAccountType::all();

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $com_id = session('logged_session_data.company_id');

            //$accounts = SysChartofAccounts::where('status',1)->wherein('company_id',$company_id)->get();
            $accounts = SysChartofAccounts::where('main_account_id', 0)->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->orderByRaw('CASE WHEN status = 2 THEN 1 ELSE 0 END')->orderBy('group', 'asc')
                ->orderBy('subgroup', 'asc')
                ->orderBy('subgroup2', 'asc')->get();
            $accounts2 = SysChartofAccounts::select('id', 'account_name', 'account_code')->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->where('account_code', 'like', 'ACC%')->get();

            $accountgroupsub2 = SysAccountGroupSub2::where('status', 1)->get();

            $account_list = SysChartofAccounts::whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")
                ->where('account_code', 'like', 'ACC%')->where('main_account_id', '=', 0)->get();

            $account_tran = SysChartofAccountsTransaction::select('account_id', 'transaction_date', 'debit_amount', 'credit_amount')->where('company_id', $com_id)->where('transaction_type', 'openingbalance')->get();

            //$roles = Role::where('active_status', '=', '1')->where('id',2)->get();
            //$paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();
            //$staffs = SmStaff::select('id','full_name')->where('active_status', '=', '1')->whereIn('designation_id', array(9, 1, 2,3))->get();

            return view('backEnd.chart-of-accounts.chartofaccountsadd', compact('accounts', 'accountgroupsub2', 'accounts2', 'account_list', 'account_tran'));

        } catch (\Throwable $th) {
            return $th;
        }

        /*$accounts = SysChartofAccounts::all();
        $accountgroupsub = SysAccountGroupSub::all();
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($accounts, null);
        }
        return view('backEnd.chart-of-accounts.chartofaccountsadd', compact('accounts','accountgroupsub'));
        */

    }

    public function get_subgroup2(Request $request)
    {
        $select_sub_group = SysAccountGroupSub2::select('id', 'title')->where('sub_id', $request->subgroup)->get();
        return response()->json([$select_sub_group]);
    }

    public function store(Request $request)
    {

        session(['opening_balance_date' => $request->opening_balance_date]);
        if ($request->account_code == "") {
            Toastr::error('Account Code Missing', 'Failed');
            return redirect()->back();
        }
        if ($request->account_name == "") {
            Toastr::error('Account Name Missing', 'Failed');
            return redirect()->back();
        }
        //if($request->group_id_sub==""){ Toastr::error('Account Type Missing', 'Failed'); return redirect()->back(); }
        //if($request->subgroup==""){ Toastr::error('Account Group Missing', 'Failed'); return redirect()->back(); }
        if ($request->subgroup2 == "") {
            Toastr::error('Account Sub Group Missing', 'Failed');
            return redirect()->back();
        }

        $input = $request->all();
        // $validator = Validator::make($input, [
        //     'subgroup'=> "required",
        //     'account_name'=> "required",
        // ]);

        // if ($validator->fails()) {
        //     if (ApiBaseMethod::checkUrl($request->fullUrl())) {
        //         return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
        //     }
        //     return redirect()->back()
        //         ->withErrors($validator)
        //         ->withInput();
        // }

        $valid = SysChartofAccounts::where(
            [
                'account_name' => $request->account_name,
                'subgroup2' => $request->subgroup2,
                'company_id' => session('logged_session_data.company_id'),
            ]
        )->get();


        try {
            DB::beginTransaction();
            // remove comma from amount fields
            $amount_dr = str_replace(',', '', $request->debit_amount);
            $amount_cr = str_replace(',', '', $request->credit_amount);



            $openingBalanceDate = $request->input('opening_balance_date');
            if (empty($openingBalanceDate)) {
                $openingBalanceDate = $this->getCompanyOpeningBalanceDateDmy();
            }
            if ($amount_cr == 0 && $amount_dr == 0) {
                $openingBalanceDate = $this->getCompanyOpeningBalanceDateDmy();
            }



            if (count($valid) == 0) {
                $accounts = new SysChartofAccounts();
                $accounts->account_code = SysHelper::get_new_account_code();
                $accounts->account_name = $request->account_name;
                $groups = SysAccountGroupSub2::select('group_id', 'sub_id')->where('id', $request->subgroup2)->first();
                $accounts->group = $groups->group_id;
                $accounts->subgroup = $groups->sub_id;
                $accounts->subgroup2 = $request->subgroup2;
                $accounts->department_id = $request->department_id;
                $accounts->yes_no = $request->credit_account_status;
                //$accounts->account_type = $request->account_type;
                //$accounts->billwise = $request->billwise;
                //$accounts->debitlimit = $request->debitlimit;
                $accounts->status = 1;
                $accounts->company_id = session('logged_session_data.company_id');
                $accounts->company_access = session('logged_session_data.company_id');
                $accounts->created_by = Auth::user()->id;
                $accounts->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                $accounts->beneficiary_name = $request->beneficiary_name;
                $accounts->bank_name = $request->bank_name;
                $accounts->acc_no = $request->acc_no;
                $accounts->iban = $request->iban;
                $accounts->swift_code = $request->swift_code;
                $accounts->routing_code = $request->routing_code;
                $accounts->branch = $request->branch;
                $accounts->branch_location = $request->branch_location;
                $accounts->stl_dept = $request->stl_dept;
                $accounts->stl = $request->stl;
                if ($request->stl == 0) {
                    $accounts->stl_limit = 0;
                } else {
                    $accounts->stl_limit = floatval(str_replace(',', '', $request->stl_limit));
                }
                $results = $accounts->save();
                $accounts->id;

                SysHelper::trn_chartof_accounts_transaction($accounts->id, $accounts->id, 'OPB-' . $accounts->id, Carbon::createFromFormat('d/m/Y', $openingBalanceDate)->format('Y-m-d'), 'openingbalance', $amount_dr, $amount_cr, 'Opening balance b/d', 1, 0, "", 0);

                // Clear account modal cache
                $com_id = session('logged_session_data.company_id');
                cache()->forget("account_modal_data_{$com_id}");

                DB::commit();

                $is_bank = SysAccountGroupSub2::
                    where('id', $request->subgroup2)
                    ->first();



                if (strtolower($is_bank->title) == 'bank') {
                    $company_bank_id = SysHelper::CreateCompanyBank($accounts->id);
                    $accounts->company_bank_id = $company_bank_id;
                    $accounts->save();
                }

                // store cheque template if provided
                if ($request->subgroup2 == 6 && $request->has('cheque_company_top')) {
                    $chartId = $accounts->id;
                    $payload = [
                        'company_top' => $request->cheque_company_top,
                        'company_left' => $request->cheque_company_left,
                        'date_top' => $request->cheque_date_top,
                        'date_left' => $request->cheque_date_left,
                        'amount_w_top' => $request->cheque_amount_w_top,
                        'amount_w_left' => $request->cheque_amount_w_left,
                        'amount_top' => $request->cheque_amount_top,
                        'amount_left' => $request->cheque_amount_left,
                        'font_size' => $request->cheque_font_size,
                        'company_id' => session('logged_session_data.company_id'),
                        'status' => 1,
                        'updated_by' => Auth::user()->id,
                        'updated_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                    ];
                    $existing = DB::table('sys_payment_cheque_template')->where('bank_id', $chartId)->first();
                    if ($existing) {
                        DB::table('sys_payment_cheque_template')->where('bank_id', $chartId)->update($payload);
                    } else {
                        $payload['bank_id'] = $chartId;
                        $payload['created_by'] = Auth::user()->id;
                        $payload['created_at'] = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                        DB::table('sys_payment_cheque_template')->insert($payload);
                    }
                }

                Toastr::success('Account Added Successful', 'Success');
                return redirect()->back();

            } else {
                DB::rollBack();
                Toastr::error('Account Name already Exists', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }

    public function edit(Request $request, $id)
    {
        try {
            $com_id = session('logged_session_data.company_id');
            $editData = SysChartofAccounts::find($id);
            $editData_tran = SysChartofAccountsTransaction::select('transaction_date', 'debit_amount', 'credit_amount')->where('account_id', $id)->where('company_id', $com_id)->where('transaction_type', 'openingbalance')->first();
            $com_ids = SysHelper::get_company_access();
            //$accounts = SysChartofAccounts::where('status',1)->wherein('company_id',$com_ids)->get();
            $accounts = SysChartofAccounts::whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->get();


            $accountgroupsub2 = SysAccountGroupSub2::where('status', 1)->get();
            $account_list = SysChartofAccounts::whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")
                ->where('account_code', 'like', 'ACC%')->where('main_account_id', '=', 0)->get();

            $account_tran = SysChartofAccountsTransaction::select('account_id', 'transaction_date', 'debit_amount', 'credit_amount')->where('company_id', $com_id)->where('transaction_type', 'openingbalance')->get();

            return view('backEnd.chart-of-accounts.chartofaccountsadd', compact('accounts', 'editData', 'accountgroupsub2', 'editData_tran', 'account_list', 'account_tran'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function edit_accounts(Request $request, $id)
    {
        try {
            $com_id = session('logged_session_data.company_id');
            $editData = SysChartofAccounts::find($id);
            $editData_tran = SysChartofAccountsTransaction::select('transaction_date', 'debit_amount', 'credit_amount')
                ->where('account_id', $id)
                ->where('company_id', $com_id)
                ->where('transaction_type', 'openingbalance')
                ->first();

            $accountgroupsub2 = SysAccountGroupSub2::where('status', 1)->get();

            // fetch the latest cheque template for this bank/account
            $cheque_template = DB::table('sys_payment_cheque_template')
                ->where('bank_id', $id)
                ->orderBy('id', 'desc')
                ->first();

            return response()->json([
                'editData' => $editData,
                'editData_tran' => $editData_tran,
                'accountgroupsub2' => $accountgroupsub2,
                'cheque_template' => $cheque_template
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Operation Failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function edit_subaccounts(Request $request, $id)
    {
        try {


            $com_id = session('logged_session_data.company_id');

            $editData = SysChartofAccounts::find($id);
            $editData_tran = SysChartofAccountsTransaction::select('transaction_date', 'debit_amount', 'credit_amount')->where('account_id', $id)->where('company_id', $com_id)->where('transaction_type', 'openingbalance')->first();
            // $account_tran = SysChartofAccountsTransaction::select('account_id', 'transaction_date', 'debit_amount', 'credit_amount')->where('company_id', $com_id)->where('transaction_type', 'openingbalance')->get();

            return response()->json([
                'editData' => $editData,
                'editData_tran' => $editData_tran,
                // 'account_tran' => $account_tran
            ]);

            // return view('backEnd.chart-of-accounts.chartofaccountsaddsub', compact('accounts', 'sub_accounts', 'editData', 'accountgroupsub2', 'editData_tran', 'account_tran'));

        } catch (\Exception $e) {
            return response()->json(['error' => 'Operation Failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function move(Request $request)
    {
        try {

            db::beginTransaction();
            $check_tran = SysChartofAccountsTransaction::where('account_id', $request->move_account_id_subgroup)->get();
            $check_tran2 = SysItemStock::where('account_id', $request->move_account_id_subgroup)->get();
            if (count($check_tran) > 0) {
                Toastr::warning('This account has some transactions. Please remove those transactions.', 'Warning');
                return redirect()->back();
            }
            if (count($check_tran2) > 0) {
                Toastr::warning('This account has some transactions. Please remove those transactions.', 'Warning');
                return redirect()->back();
            }
            $data = SysChartofAccounts::find($request->move_account_id_subgroup);
            $groupid = SysAccountGroupSub::select('group_id')->where('id', $request->group_account)->first();

            $accountgroup2 = new SysAccountGroupSub2();
            $accountgroup2->group_id = $groupid->group_id;
            $accountgroup2->sub_id = $request->group_account;
            $accountgroup2->title = $data->account_name;
            $accountgroup2->status = 1;
            $accountgroup2->created_by = Auth::user()->id;
            $results = $accountgroup2->save();

            $check_sub = SysChartofAccounts::where('main_account_id', $request->move_account_id_subgroup)->get();
            if (count($check_sub)) {
                foreach ($check_sub as $dt) {
                    SysChartofAccounts::where('id', $dt->id)->update([
                        'account_code' => SysHelper::get_new_account_code(),
                        'main_account_id' => 0,
                        'subgroup2' => $accountgroup2->id,
                    ]);
                }
            }

            SysChartofAccounts::where('id', $request->move_account_id_subgroup)->delete();

            db::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            db::rollBack();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {

        DB::beginTransaction();

        if ($request->btnSubmit == "delete") {

            try {
                $accounts = SysChartofAccounts::find($id);
                $accounts->status = 0;
                $accounts->updated_by = Auth()->user()->id;
                $results = $accounts->update();

                Toastr::success('Operation successful', 'Success');
                return redirect()->back();


            } catch (\Throwable $th) {
                DB::rollBack();
                return $th;
            }
            //DB::table('sys_chartofaccounts')->where('id', $id)->delete();
        } else if ($request->btnSubmit == "update") {


            if ($request->account_code == "") {
                Toastr::error('Account Code Missing', 'Failed');
                return redirect()->back();
            }
            if ($request->account_name == "") {
                Toastr::error('Account Name Missing', 'Failed');
                return redirect()->back();
            }
            //if($request->group_id_sub==""){ Toastr::error('Account Type Missing', 'Failed'); return redirect()->back(); }
            //if($request->subgroup==""){ Toastr::error('Account Group Missing', 'Failed'); return redirect()->back(); }
            if ($request->subgroup2 == "") {
                Toastr::error('Account Sub Group Missing', 'Failed');
                return redirect()->back();
            }

            $input = $request->all();

            try {

                $accounts = SysChartofAccounts::find($id);

                //$accounts->account_code = $request->account_code;
                $accounts->account_name = $request->account_name;
                $groups = SysAccountGroupSub2::select('group_id', 'sub_id')->where('id', $request->subgroup2)->first();
                $accounts->group = $groups->group_id;
                $accounts->subgroup = $groups->sub_id;
                $accounts->subgroup2 = $request->subgroup2;
                $accounts->status = 1;
                $accounts->department_id = $request->department_id;
                $accounts->yes_no = $request->credit_account_status;
                //$accounts->company_id = session('logged_session_data.company_id');
                //$accounts->company_access = session('logged_session_data.company_id');
                $accounts->updated_by = Auth()->user()->id;
                $accounts->updated_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                $accounts->beneficiary_name = $request->beneficiary_name;
                $accounts->bank_name = $request->bank_name;
                $accounts->acc_no = $request->acc_no;
                $accounts->iban = $request->iban;
                $accounts->swift_code = $request->swift_code;
                $accounts->routing_code = $request->routing_code;
                $accounts->branch = $request->branch;
                $accounts->branch_location = $request->branch_location;
                $accounts->stl_dept = $request->stl_dept;
                $accounts->stl = $request->stl;
                if ($request->stl == 0) {
                    $accounts->stl_limit = 0;
                } else {
                    $accounts->stl_limit = floatval(str_replace(',', '', $request->stl_limit));
                }
                $results = $accounts->update();



                //$accounts->account_type = $request->account_type;
                //$accounts->billwise = $request->billwise;
                //$accounts->debitlimit = $request->debitlimit;


                $amount_dr = str_replace(',', '', $request->debit_amount);
                $amount_cr = str_replace(',', '', $request->credit_amount);

                $companyId = session('logged_session_data.company_id');



                $checkDataExists = DB::table('sys_chartofaccounts_transaction')
                    ->where('account_id', $id)
                    ->where('company_id', $companyId)
                    ->where('transaction_type', 'openingbalance')
                    ->where('status', 1)
                    ->get();

                $openingBalanceDate = $request->input('opening_balance_date');
                if (empty($openingBalanceDate)) {
                    $openingBalanceDate = $this->getCompanyOpeningBalanceDateDmy();
                }

                if (count($checkDataExists) > 0) {
                    // Update existing opening balance transaction
                    DB::table('sys_chartofaccounts_transaction')
                        ->where('account_id', $id)
                        ->where('company_id', $companyId)
                        ->where('transaction_type', 'openingbalance')
                        ->update([
                            'debit_amount' => $amount_dr,
                            'credit_amount' => $amount_cr,
                            'transaction_date' => Carbon::createFromFormat('d/m/Y', $openingBalanceDate)->format('Y-m-d'),
                        ]);
                } else {
                    // Insert new opening balance transaction
                    SysHelper::trn_chartof_accounts_transaction(
                        $id,
                        $id,
                        'OPB-' . $id,
                        Carbon::createFromFormat('d/m/Y', $openingBalanceDate)->format('Y-m-d'),
                        'openingbalance',
                        $amount_dr,
                        $amount_cr,
                        'Opening balance b/d',
                        1,
                        0,
                        "",
                        0
                    );
                }



                // $check_data_excist = DB::table('sys_chartofaccounts_transaction')->where('account_id',$id)->where('company_id',session('logged_session_data.company_id'))->where('transaction_type','openingbalance')->count();
                // if($check_data_excist == 0){
                //     SysHelper::trn_chartof_accounts_transaction($id,$id,'OPB-'.$id,$request->opening_balance_date,'openingbalance',$amount_dr,$amount_cr,'Opening balance b/d',1,0,"",0);
                // } else{
                //     DB::table('sys_chartofaccounts_transaction')->where('account_id',$id)->where('transaction_type','openingbalance')->update(
                //         [
                //             'debit_amount' => $amount_dr,
                //             'credit_amount' => $amount_cr,
                //             'transaction_date' => $request->opening_balance_date,
                //             'company_id' => session('logged_session_data.company_id'),
                //         ]
                //     );
                // }

                // Clear account modal cache
                $com_id = session('logged_session_data.company_id');
                cache()->forget("account_modal_data_{$com_id}");

                DB::commit();



                // insert cheque template persistence (update)
                if ($request->subgroup2 == 6 && $request->has('cheque_company_top')) {
                    $chartId = $id;
                    $tpl = [
                        'company_top' => $request->cheque_company_top,
                        'company_left' => $request->cheque_company_left,
                        'date_top' => $request->cheque_date_top,
                        'date_left' => $request->cheque_date_left,
                        'amount_w_top' => $request->cheque_amount_w_top,
                        'amount_w_left' => $request->cheque_amount_w_left,
                        'amount_top' => $request->cheque_amount_top,
                        'amount_left' => $request->cheque_amount_left,
                        'font_size' => $request->cheque_font_size,
                        'company_id' => session('logged_session_data.company_id'),
                        'status' => 1,
                        'updated_by' => Auth::user()->id,
                        'updated_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                    ];
                    $existing = DB::table('sys_payment_cheque_template')->where('bank_id', $chartId)->first();
                    if ($existing) {
                        DB::table('sys_payment_cheque_template')->where('bank_id', $chartId)->update($tpl);
                    } else {
                        $tpl['bank_id'] = $chartId;
                        $tpl['created_by'] = Auth::user()->id;
                        $tpl['created_at'] = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                        DB::table('sys_payment_cheque_template')->insert($tpl);
                    }
                }



                if ($request->pre_sub_group2 == '6' && $request->subgroup2 != '6') {
                    SysCompanyBanking::where('id', $accounts->company_bank_id)->delete();
                } else if ($request->subgroup2 == '6') {
                    $banking_id = SysHelper::UpdateCompanyBank($id);
                    $accounts->company_bank_id = $banking_id;
                    $accounts->save();
                }





                Toastr::success('Operation successful', 'Success');
                return redirect()->back();

            } catch (\Exception $e) {
                DB::rollBack();
                dd($e);
                return $e;
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        } else {
            DB::rollBack();
            dd($request->btnSubmit);
            Toastr::error('Oops!! something went wrong', 'Failed');
            return redirect()->back();
        }
    }

    public function delete($id)
    {
        try {

            $check_tran = SysChartofAccountsTransaction::where('account_id', $id)->get();
            $check_tran2 = SysItemStock::where('account_id', $id)->get();
            if (count($check_tran) > 0) {
                Toastr::warning('This account has some transactions. Please remove those transactions.', 'Warning');
                return redirect()->back();
            }
            if (count($check_tran2) > 0) {
                Toastr::warning('This account has some transactions. Please remove those transactions.', 'Warning');
                return redirect()->back();
            }

            SysChartofAccounts::where('id', $id)->update(['status' => 2]);
            $code = SysChartofAccounts::select('account_code')->where('id', $id)->first();
            SysCustSuppl::where('code', $code->account_code)->update(['status' => 2]);

            // Clear account modal cache
            $com_id = session('logged_session_data.company_id');
            cache()->forget("account_modal_data_{$com_id}");

            Toastr::success('Deactivated successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function restore($id)
    {
        try {
            SysChartofAccounts::where('id', $id)->update(['status' => 1]);
            $code = SysChartofAccounts::select('account_code')->where('id', $id)->first();
            SysCustSuppl::where('code', $code->account_code)->update(['status' => 1]);

            // Clear account modal cache
            $com_id = session('logged_session_data.company_id');
            cache()->forget("account_modal_data_{$com_id}");

            Toastr::success('Activated successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function getchartofaccountsinfo(Request $request)
    {
        try {
            $ret = DB::table('sys_chartofaccounts as ca')
                ->select(
                    'ca.id',
                    'ca.account_code',
                    'ca.account_name',
                    'cs.address',
                    'cs.address2',
                    'cs.contcat_person',
                    DB::raw("
    CONCAT_WS(' ',
        cs.customer_salutation,
        cs.first_name,
        cs.last_name
    ) AS contact_person
")
                    ,
                    'cs.contcat_number',
                    'cs.mobile',
                    'cs.email',
                    'cs.payment_terms',
                    'cs.vat_country',
                    'cs.vat_number',
                    'cs.vat_state',
                    'cs.vat_type',
                    'cs.purchase_type',
                    'cs.customer_type',
                    'cs.sale_type',
                    'cs.supplier_type',
                    'cs.vat_percentage',
                    'cs.customer_salutation',
                    'cs.first_name',
                    'cs.last_name',
                    DB::raw("
    CONCAT_WS(', ',
        addr.flat_office_no,
        addr.building_name,
        addr.area,
        st.name,
        co.name,
        addr.zip_code
    ) AS shipping_address
")

                )
                ->leftjoin('sys_cust_suppl as cs', 'cs.code', 'ca.account_code')
                ->leftJoin('sys_cust_suppl_addressbook as addr', 'addr.cust_suppl_id', 'cs.id')
                // ✅ VAT State Join
                ->leftJoin('sys_states as st', 'st.id', '=', 'cs.vat_state')

                // ✅ VAT Country Join
                ->leftJoin('sys_countries as co', 'co.id', '=', 'cs.vat_country')

                ->where('ca.id', $request->id)->get();

            return json_encode(array('data' => $ret));

        } catch (\Exception $e) {
            dd($e);
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    // IMPORT START    
    public function chartofaccounts_import(Request $request)
    {
        try {
            $data = DB::table('sys_chartofaccounts_import as i')->select('i.*')->where('i.status', 1)->where('i.company_id', session('logged_session_data.company_id'))->get();
            $account_name = DB::table('sys_chartofaccounts')->select('account_name')->where('company_id', session('logged_session_data.company_id'))->get();
            $account_group = DB::table('sys_account_group')->select('id', 'title')->get();
            $account_group_sub = DB::table('sys_account_group_sub')->select('id', 'title')->get();
            $account_group_sub2 = DB::table('sys_account_group_sub2')->select('id', 'title')->get();

            return view('backEnd.chart-of-accounts.importchartofaccounts', compact('data', 'account_name', 'account_group', 'account_group_sub', 'account_group_sub2'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function chartofaccounts_import_list(Request $request)
    {
        try {
          

            DB::beginTransaction();
            $selected_file = "";
            if ($request->file('import_file') != "") {
                $file = $request->file('import_file');
                $selected_file = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/product_upload/', $selected_file);
                $selected_file = 'public/uploads/product_upload/' . $selected_file;
                //return  $selected_file;
            }

            $objPHPExcel = PHPExcel_IOFactory::load($selected_file);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow();

            $dataArray = $objPHPExcel->getActiveSheet()->toArray();
            $data = [];

            //return count($dataArray[0]);
            /*->rangeToArray(
            'A1:C4',     // The worksheet range that we want to retrieve
            NULL,        // Value that should be returned for empty cells
            TRUE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            TRUE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            TRUE         // Should the array be indexed by cell row and cell column
            );*/
           

            for ($i = 1; $i < count($dataArray); $i++) {

                $data[] = [
                    'account_name' => $dataArray[$i][0],
                    'subgroup2' => $dataArray[$i][1],
                    'debit_amount' => $dataArray[$i][2],
                    'credit_amount' => $dataArray[$i][3],
                    'date' => SysHelper::normalizeToYmd($dataArray[$i][4]),
                    'yes_no' => $dataArray[$i][6],
                    'department' => $dataArray[$i][5],
                    'created_by' => Auth()->user()->id,
                    'company_id' => session('logged_session_data.company_id'),
                ];

            }

            SysChartofAccountsImport::insert($data);
            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function chartofaccounts_import_clear(Request $request)
    {
        try {
            SysChartofAccountsImport::where('company_id', session('logged_session_data.company_id'))->delete();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function chartofaccounts_import_data(Request $request)
    {
        try {
            DB::beginTransaction();
            $account_name = DB::table('sys_chartofaccounts')->where('company_id', session('logged_session_data.company_id'))->pluck('account_name');
            $part_number_id = DB::table('sm_items')->select('id', 'part_number')->where('company_id', session('logged_session_data.company_id'))->get();

            $data = DB::table('sys_chartofaccounts_import as i')->select('i.*')->where('i.status', 1)->where('i.company_id', session('logged_session_data.company_id'))
                ->wherenotIn('i.account_name', $account_name)->get();




            $group_id = 0;
            $group_sub_id = 0;
            $group_sub2_id = 0;

            $company_access = session('logged_session_data.company_id');
            //if(session('logged_session_data.company_id') != 1){ $company_access = '1,'.session('logged_session_data.company_id'); }
            $import_date = Carbon::now('+04:00')->format('Y-m-d H:i:s');

            if (count($data) > 0) {
                foreach ($data as $dt) {

                    // $dt->subgroup2

                    $group_sub2_id = DB::table('sys_account_group_sub2')->select('id', 'title', 'group_id', 'sub_id')->where('title', $dt->subgroup2)->first();


                    $group_sub_id = $group_sub2_id->sub_id;
                    $group_id = $group_sub2_id->group_id;





                    if ($group_id == "") {
                        $group_id = 0;
                    }
                    if ($group_sub_id == "") {
                        $group_sub_id = 0;
                    }


                    $accounts = new SysChartofAccounts();
                    $accounts->account_code = SysHelper::get_new_account_code();
                    $accounts->account_name = $dt->account_name;
                    $accounts->group = $group_id;
                    $accounts->subgroup = $group_sub_id;
                    $accounts->subgroup2 = $group_sub2_id->id;
                    $accounts->status = 1;
                    $accounts->company_id = $dt->company_id;
                    $accounts->company_access = $company_access;
                    $accounts->created_by = Auth::user()->id;
                    $accounts->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                    // if yes then 1 else 0 also handle small case space etc
                    if($dt->yes_no == 'Yes' || $dt->yes_no == 'yes' || $dt->yes_no == 'YES' || $dt->yes_no == 'Yes '){
                        $accounts->yes_no = 1;
                    }elseif($dt->yes_no == 'No' || $dt->yes_no == 'no' || $dt->yes_no == 'NO' || $dt->yes_no == 'No '){
                        $accounts->yes_no = 0;
                    }else{
                        $accounts->yes_no = null;
                    }
                   

                    $accounts->department_id    = SysHelper::getDepartmentID($dt->department);


                    $accounts->save();
                    $accounts->toArray();

                    $amount_dr = $dt->debit_amount;
                    $amount_cr = $dt->credit_amount;
                    if ($amount_dr == '') {
                        $amount_dr = '0.00';
                    }
                    if ($amount_cr == '') {
                        $amount_cr = '0.00';
                    }

                    $import_date = $dt->date;

                    SysHelper::trn_chartof_accounts_transaction($accounts->id, $accounts->id, 'OPB-' . $accounts->id, $import_date, 'openingbalance', $amount_dr, $amount_cr, 'Opening balance b/d', 1, 0, "", 0);
                }


                SysChartofAccountsImport::where('company_id', session('logged_session_data.company_id'))->delete();
                DB::commit();
            } else {

                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
            Toastr::success('Accounts Imported Successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    // IMPORT END

    // IMPORT SUB START    
    public function chartofaccounts_import_sub(Request $request)
    {
        try {
            $data = DB::table('sys_chartofaccounts_import_sub as i')->select('i.*')->where('i.status', 1)->where('i.company_id', session('logged_session_data.company_id'))->get();
            $account_name = DB::table('sys_chartofaccounts')->select('account_name')->where('company_id', session('logged_session_data.company_id'))->get();
            $account_group = DB::table('sys_account_group')->select('id', 'title')->get();
            $account_group_sub = DB::table('sys_account_group_sub')->select('id', 'title')->get();
            $account_group_sub2 = DB::table('sys_account_group_sub2')->select('id', 'title')->get();

            return view('backEnd.chart-of-accounts.importchartofaccounts_sub', compact('data', 'account_name', 'account_group', 'account_group_sub', 'account_group_sub2'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function chartofaccounts_import_sub_list(Request $request)
    {
        try {
            DB::beginTransaction();
            $selected_file = "";
            if ($request->file('import_file') != "") {
                $file = $request->file('import_file');
                $selected_file = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/product_upload/', $selected_file);
                $selected_file = 'public/uploads/product_upload/' . $selected_file;
                //return  $selected_file;
            }

            $objPHPExcel = PHPExcel_IOFactory::load($selected_file);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow();

            $dataArray = $objPHPExcel->getActiveSheet()->toArray();

            //return count($dataArray[0]);
            /*->rangeToArray(
            'A1:C4',     // The worksheet range that we want to retrieve
            NULL,        // Value that should be returned for empty cells
            TRUE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            TRUE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            TRUE         // Should the array be indexed by cell row and cell column
            );*/

            for ($i = 1; $i < count($dataArray); $i++) {

                //for($j=0; $j < count($dataArray[0]); $j++){
                $data[] = [
                    'account_name' => $dataArray[$i][0],
                    'sub_account_name' => $dataArray[$i][1],
                    'debit_amount' => $dataArray[$i][2],
                    'credit_amount' => $dataArray[$i][3],
                    'sub_account_date' => SysHelper::normalizeToYmd($dataArray[$i][4]),
                    'yes_no' => $dataArray[$i][6],
                    'department' => $dataArray[$i][5],
                    'created_by' => Auth()->user()->id,
                    'company_id' => session('logged_session_data.company_id'),

                ];
                //}
                //$data2[]=$data;

            }

            SysChartofAccountsImportSub::insert($data);
            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function chartofaccounts_import_sub_clear(Request $request)
    {
        try {
            SysChartofAccountsImportSub::where('company_id', session('logged_session_data.company_id'))->delete();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function chartofaccounts_import_sub_data(Request $request)
    {
        try {
            DB::beginTransaction();
            $account_name = DB::table('sys_chartofaccounts')->where('company_id', session('logged_session_data.company_id'))->pluck('account_name');
            $account_id = DB::table('sys_chartofaccounts')->where('company_id', session('logged_session_data.company_id'))->wherein('account_name', $account_name)->get();


            $data = DB::table('sys_chartofaccounts_import_sub as i')->select('i.*')->where('i.status', 1)->where('i.company_id', session('logged_session_data.company_id'))
                ->wherenotIn('i.sub_account_name', $account_name)->get();



            $group_id = 0;
            $group_sub_id = 0;
            $group_sub2_id = 0;

            $company_access = session('logged_session_data.company_id');
            //if(session('logged_session_data.company_id') != 1){ $company_access = '1,'.session('logged_session_data.company_id'); }
            $import_date = Carbon::now('+04:00')->format('Y-m-d H:i:s');

            if (count($data) > 0) {
                foreach ($data as $dt) {

                    $main_account_id = DB::table('sys_chartofaccounts')->where('company_id', session('logged_session_data.company_id'))->where('account_name', $dt->account_name)->first();

                    $group_id = $main_account_id->group;
                    $group_sub_id = $main_account_id->subgroup;
                    $group_sub2_id = $main_account_id->subgroup2;



                    if ($group_id == "") {
                        $group_id = 0;
                    }
                    if ($group_sub_id == "") {
                        $group_sub_id = 0;
                    }
                    if ($group_sub2_id == "") {
                        $group_sub2_id = 0;
                    }

                    $accounts = new SysChartofAccounts();
                    $accounts->account_code = SysHelper::get_new_sub_account_code();
                    $accounts->account_name = $dt->sub_account_name;
                    $accounts->group = $group_id;
                    $accounts->subgroup = $group_sub_id;
                    $accounts->subgroup2 = $group_sub2_id;
                    $accounts->status = 1;
                    $accounts->main_account_id = $main_account_id->id;
                    $accounts->department_id = SysHelper::getDepartmentID($dt->department);
                
                    if($dt->yes_no == 'Yes' || $dt->yes_no == 'yes' || $dt->yes_no == 'YES' || $dt->yes_no == 'Yes '){
                        $accounts->yes_no = 1;
                    }elseif($dt->yes_no == 'No' || $dt->yes_no == 'no' || $dt->yes_no == 'NO' || $dt->yes_no == 'No '){
                        $accounts->yes_no = 0;
                    }else{
                        $accounts->yes_no = null;
                    }
                    $accounts->company_id = $dt->company_id;
                    $accounts->company_access = $company_access;
                    $accounts->created_by = Auth::user()->id;
                    $accounts->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                    $accounts->save();
                    $accounts->toArray();

                    $amount_dr = $dt->debit_amount;
                    $amount_cr = $dt->credit_amount;
                    if ($amount_dr == '') {
                        $amount_dr = '0.00';
                    }
                    if ($amount_cr == '') {
                        $amount_cr = '0.00';
                    }
                    //return date('Y-m-d', strtotime($dt->sub_account_date));

                    SysHelper::trn_chartof_accounts_transaction($accounts->id, $accounts->id, 'OPB-' . $accounts->id, SysHelper::normalizeToYmd($dt->sub_account_date), 'openingbalance', $amount_dr, $amount_cr, 'Opening balance b/d', 1, 0, "", 0);
                }


                SysChartofAccountsImportSub::where('company_id', session('logged_session_data.company_id'))->delete();
                DB::commit();
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
            Toastr::success('Accounts Imported Successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    // IMPORT SUB END



    //Sub Account Start
    public function chartofaccounts_add_sub(Request $request)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $com_id = session('logged_session_data.company_id');
            $accounts = SysChartofAccounts::select('id', 'account_name', 'account_code')->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->where('main_account_id', 0)->where('account_code', 'like', 'ACC%')->get();
            $sub_accounts = SysChartofAccounts::whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->where('main_account_id', '!=', 0)->orderBy('group', 'asc')->orderBy('subgroup', 'asc')->orderBy('subgroup2', 'asc')->get();
            $accountgroupsub2 = SysAccountGroupSub2::where('status', 1)->get();

            $account_list = SysChartofAccounts::whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")
                ->where('account_code', 'like', 'SACC%')->where('main_account_id', '!=', 0)->get();

            $account_tran = SysChartofAccountsTransaction::select('account_id', 'transaction_date', 'debit_amount', 'credit_amount')->where('company_id', $com_id)->where('transaction_type', 'openingbalance')->get();

            return view('backEnd.chart-of-accounts.chartofaccountsaddsub', compact('accounts', 'sub_accounts', 'accountgroupsub2', 'account_list', 'account_tran'));

        } catch (\Throwable $th) {
            return $th;
        }
    }


    public function store_sub(Request $request)
    {
        if ($request->main_account_id == "") {
            Toastr::error('Main Account Missing', 'Failed');
            return redirect()->back();
        }
        if ($request->account_code == "") {
            Toastr::error('Account Code Missing', 'Failed');
            return redirect()->back();
        }
        if ($request->account_name == "") {
            Toastr::error('Account Name Missing', 'Failed');
            return redirect()->back();
        }
        $results = 0;


        session(['opening_balance_date_sub' => $request->opening_balance_date]);

        $input = $request->all();
        $valid = SysChartofAccounts::where(
            [
                'account_name' => $request->account_name,
                'main_account_id' => $request->main_account_id,
            ]
        )->get();
        try {
            // $amount_dr = str_replace(',', '', $request->debit_amount);
            $amount_dr = str_replace(',', '', $request->debit_amount);
            $amount_cr = str_replace(',', '', $request->credit_amount);

            $openingBalanceDate = $request->input('opening_balance_date');
            if (empty($openingBalanceDate)) {
                $openingBalanceDate = $this->getCompanyOpeningBalanceDateDmy();
            }

            if ($amount_dr == 0 && $amount_cr == 0) {
                $openingBalanceDate = $this->getCompanyOpeningBalanceDateDmy();
            }

            if (count($valid) == 0) {
                $get_acc_det = SysChartofAccounts::where('id', $request->main_account_id)->first();
                $accounts = new SysChartofAccounts();
                $accounts->account_code = SysHelper::get_new_sub_account_code();
                $accounts->account_name = $request->account_name;
                $accounts->group = $get_acc_det->group;
                $accounts->subgroup = $get_acc_det->subgroup;
                $accounts->subgroup2 = $get_acc_det->subgroup2;
                $accounts->main_account_id = $request->main_account_id;
                $accounts->department_id = $request->department_id;
                $accounts->yes_no = $request->credit_account_status;
                //$accounts->account_type = $request->account_type;
                //$accounts->billwise = $request->billwise;
                //$accounts->debitlimit = $request->debitlimit;
                $accounts->status = 1;
                $accounts->company_id = session('logged_session_data.company_id');
                $accounts->company_access = session('logged_session_data.company_id');
                $accounts->created_by = Auth::user()->id;
                $accounts->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                $results = $accounts->save();
                $accounts->id;

                SysHelper::trn_chartof_accounts_transaction($accounts->id, $accounts->id, 'OPB-' . $accounts->id, Carbon::createFromFormat('d/m/Y', $openingBalanceDate)->format('Y-m-d'), 'openingbalance', $amount_dr, $amount_cr, 'Opening balance b/d', 1, 0, "", 0);
            }

            if ($results) {
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function store_sub_employee(Request $request)
    {
        try {

            $com_id = session('logged_session_data.company_id');
            $staff = SmStaff::select('id', 'full_name', 'department_id')
                ->where('id', $request->employee_id)
                ->where('active_status', 1)
                ->where(function ($query) use ($com_id) {
                    $query->where('company_id', $com_id)
                        ->orWhereRaw("find_in_set($com_id,company_access)");
                })
                ->first();

            if (!$staff) {
                Toastr::error('Employee not found for this company', 'Failed');
                return redirect()->back();
            }

            
          
            $employeeName = ucwords($staff->full_name);
            if (isset($request->account_id_emp)) {
              

            } else {
                dd($request->all());
                Toastr::error('Operation Failed. Please Select Accounts', 'Failed');
                return redirect()->back();
            }
            DB::beginTransaction();
            foreach ($request->account_id_emp as $emp) {
                $det = SysHelper::get_account_details_for_employee_sub_add($com_id, $emp);
                dd($det);
                if (isset($det)) {
                    if ($det == "no_data_found") {

                        DB::rollBack();
                        Toastr::error('Operation Failed. Accounts not found', 'Failed');
                        return redirect()->back();
                    }
                    $valid = SysChartofAccounts::where([
                        'account_name' => $employeeName . ' ' . $det->sub_account_name,
                        'company_id' => $com_id
                    ])->get();
                    if (count($valid) > 0) {
                        DB::rollBack();
                        Toastr::error('Accounts Name Already Excist', 'Failed');
                        return redirect()->back();
                    }

                    $accounts = new SysChartofAccounts();
                    $accounts->account_code = SysHelper::get_new_sub_account_code();
                    $accounts->account_name = $employeeName . ' ' . $det->sub_account_name;
                    $accounts->group = $det->group;
                    $accounts->subgroup = $det->subgroup;
                    $accounts->subgroup2 = $det->subgroup2;
                    $accounts->main_account_id = $det->id;
                    $accounts->department_id = $staff->department_id;
                    
                    //$accounts->account_type = $request->account_type;
                    //$accounts->billwise = $request->billwise;
                    //$accounts->debitlimit = $request->debitlimit;
                    $accounts->status = 1;
                    $accounts->company_id = session('logged_session_data.company_id');
                    $accounts->company_access = session('logged_session_data.company_id');
                    $accounts->created_by = Auth::user()->id;
                    $accounts->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                    $results = $accounts->save();
                    $accounts->id;
                }
            }
            DB::commit();
            Toastr::success('Employee Account Added', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function edit_sub(Request $request, $id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $com_id = session('logged_session_data.company_id');

            $editData = SysChartofAccounts::find($id);
            $editData_tran = SysChartofAccountsTransaction::select('transaction_date', 'debit_amount', 'credit_amount')->where('account_id', $id)->where('company_id', $com_id)->where('transaction_type', 'openingbalance')->first();
            $com_ids = SysHelper::get_company_access();
            $accounts = SysChartofAccounts::select('id', 'account_name', 'account_code')->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->where('main_account_id', 0)->where('account_code', 'like', 'ACC%')->get();
            $sub_accounts = SysChartofAccounts::whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->where('main_account_id', '!=', 0)->get();
            $accountgroupsub2 = SysAccountGroupSub2::where('status', 1)->get();

            $account_tran = SysChartofAccountsTransaction::select('account_id', 'transaction_date', 'debit_amount', 'credit_amount')->where('company_id', $com_id)->where('transaction_type', 'openingbalance')->get();

            return view('backEnd.chart-of-accounts.chartofaccountsaddsub', compact('accounts', 'sub_accounts', 'editData', 'accountgroupsub2', 'editData_tran', 'account_tran'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function move_maintosub(Request $request)
    {
        //return $request->all();
        try {
            SysChartofAccounts::where('id', $request->move_account_id)->update([
                'account_code' => SysHelper::get_new_sub_account_code(),
                'main_account_id' => $request->main_account_id,
            ]);
            Toastr::success('Main Account Moved to Sub Account', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function move_sub(Request $request)
    {
        try {


            $groups = SysAccountGroupSub2::select('group_id', 'sub_id')->where('id', $request->group_account)->first();




            SysChartofAccounts::where('id', $request->move_account_id_subgroup)->update([
                'account_code' => SysHelper::get_new_account_code(),
                'main_account_id' => 0,
                'group' => $groups->group_id,
                'subgroup' => $groups->sub_id,
                'subgroup2' => $request->group_account,
            ]);
            Toastr::success('Sub Account Moved to Main', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update_sub(Request $request, $id)
    {
        if ($request->btnSubmit == "delete") {

            try {
                $accounts = SysChartofAccounts::find($id);
                $accounts->status = 0;
                $accounts->updated_by = Auth()->user()->id;
                $accounts->updated_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                $results = $accounts->update();

                Toastr::success('Operation successful', 'Success');
                return redirect()->back();

            } catch (\Throwable $th) {
                return $th;
            }
            //DB::table('sys_chartofaccounts')->where('id', $id)->delete();
        } else if ($request->btnSubmit == "update") {

            if ($request->account_code == "") {
                Toastr::error('Account Code Missing', 'Failed');
                return redirect()->back();
            }
            if ($request->account_name == "") {
                Toastr::error('Account Name Missing', 'Failed');
                return redirect()->back();
            }
            //if($request->group_id_sub==""){ Toastr::error('Account Type Missing', 'Failed'); return redirect()->back(); }
            //if($request->subgroup==""){ Toastr::error('Account Group Missing', 'Failed'); return redirect()->back(); }
            if ($request->main_account_id == "") {
                Toastr::error('Main Account Missing', 'Failed');
                return redirect()->back();
            }

            $input = $request->all();

            try {

                $get_acc_det = SysChartofAccounts::where('id', $request->main_account_id)->first();
                $accounts = SysChartofAccounts::find($id);

                //$accounts->account_code = $request->account_code;
                $accounts->account_name = $request->account_name;
                $accounts->group = $get_acc_det->group;
                $accounts->subgroup = $get_acc_det->subgroup;
                $accounts->subgroup2 = $get_acc_det->subgroup2;
                $accounts->status = 1;
                $accounts->main_account_id = $request->main_account_id;
                $accounts->department_id = $request->department_id;
                $accounts->yes_no = $request->credit_account_status;
                //$accounts->company_id = session('logged_session_data.company_id');
                //$accounts->company_access = session('logged_session_data.company_id');
                $accounts->updated_by = Auth()->user()->id;
                $accounts->updated_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                $results = $accounts->update();
                //$accounts->account_type = $request->account_type;
                //$accounts->billwise = $request->billwise;
                //$accounts->debitlimit = $request->debitlimit;


                //replace comma from amount
                $amount_dr = str_replace(',', '', $request->debit_amount);
                $amount_cr = str_replace(',', '', $request->credit_amount);
                $openingBalanceDate = $request->input('opening_balance_date');
                if (empty($openingBalanceDate)) {
                    $openingBalanceDate = $this->getCompanyOpeningBalanceDateDmy();
                }

                $check_data_excist = DB::table('sys_chartofaccounts_transaction')->where('account_id', $id)->where('company_id', session('logged_session_data.company_id'))->where('transaction_type', 'openingbalance')->count();
                if ($check_data_excist == 0) {
                    SysHelper::trn_chartof_accounts_transaction($id, $id, 'OPB-' . $id, Carbon::createFromFormat('d/m/Y', $openingBalanceDate)->format('Y-m-d'), 'openingbalance', $amount_dr, $amount_cr, 'Opening balance b/d', 1, 0, "", 0);
                } else {
                    DB::table('sys_chartofaccounts_transaction')->where('account_id', $id)->where('company_id', session('logged_session_data.company_id'))->where('transaction_type', 'openingbalance')->update(
                        [
                            'debit_amount' => $amount_dr,
                            'credit_amount' => $amount_cr,
                            'transaction_date' => Carbon::createFromFormat('d/m/Y', $openingBalanceDate)->format('Y-m-d'),
                        ]
                    );
                }


                if ($results) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect()->back();
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }

            } catch (\Exception $e) {
                return $e;
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        } else {
            Toastr::error('Oops!! something went wrong', 'Failed');
            return redirect()->back();
        }
    }
    public function delete_sub($id)
    {
        try {
            SysChartofAccounts::where('id', $id)->update(['status' => 2]);
            $code = SysChartofAccounts::select('account_code')->where('id', $id)->first();
            SysCustSuppl::where('code', $code->account_code)->update(['status' => 2]);
            Toastr::success('Deactivated successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function restore_sub($id)
    {
        try {
            SysChartofAccounts::where('id', $id)->update(['status' => 1]);
            $code = SysChartofAccounts::select('account_code')->where('id', $id)->first();
            SysCustSuppl::where('code', $code->account_code)->update(['status' => 1]);
            Toastr::success('Activated successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    //Sub Account end

    // Opening Balance Start
    public function openingBalanceSearch(Request $request)
    {
        try {
            $com_id = session('logged_session_data.company_id');
            $query = trim((string) $request->get('q', ''));

            if (strlen($query) < 2) {
                return response()->json(['data' => []]);
            }

            $results = SysChartofAccounts::select('id', 'account_code', 'account_name', 'main_account_id')
                ->whereRaw("find_in_set(?, company_access)", [$com_id])
                ->where('status', 1)
                ->where(function ($builder) use ($query) {
                    $builder->where('account_name', 'like', "%{$query}%")
                        ->orWhere('account_code', 'like', "%{$query}%");
                })
                ->orderBy('account_code', 'asc')
                ->limit(20)
                ->get()
                ->map(function ($row) {
                    return [
                        'id' => $row->id,
                        'account_code' => $row->account_code,
                        'account_name' => $row->account_name,
                        'type' => (int) $row->main_account_id === 0 ? 'Account' : 'Sub Account',
                    ];
                });

            return response()->json(['data' => $results]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unable to load accounts right now.'], 500);
        }
    }

    public function openingBalanceDetails($id)
    {
        try {
            $com_id = session('logged_session_data.company_id');
            $account = SysChartofAccounts::select('id', 'account_code', 'account_name', 'main_account_id')
                ->whereRaw("find_in_set(?, company_access)", [$com_id])
                ->where('status', 1)
                ->where('id', $id)
                ->first();

            if (!$account) {
                return response()->json(['message' => 'Account not found.'], 404);
            }

            $opening = SysChartofAccountsTransaction::select('debit_amount', 'credit_amount', 'transaction_date')
                ->where('company_id', $com_id)
                ->where('account_id', $id)
                ->where('transaction_type', 'openingbalance')
                ->where('status', 1)
                ->first();

            return response()->json([
                'id' => $account->id,
                'account_code' => $account->account_code,
                'account_name' => $account->account_name,
                'type' => (int) $account->main_account_id === 0 ? 'Account' : 'Sub Account',
               'debit_amount'  => number_format((float) ($opening->debit_amount ?? 0), 2, '.', ''),
'credit_amount' => number_format((float) ($opening->credit_amount ?? 0), 2, '.', ''),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unable to load opening balance details.'], 500);
        }
    }

    public function openingBalanceUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|integer',
            'debit_amount' => 'required',
            'credit_amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $com_id = session('logged_session_data.company_id');
            $accountId = (int) $request->account_id;

            $account = SysChartofAccounts::whereRaw("find_in_set(?, company_access)", [$com_id])
                ->where('status', 1)
                ->where('id', $accountId)
                ->first();

            if (!$account) {
                DB::rollBack();
                return response()->json(['message' => 'Account not found.'], 404);
            }

            $amountDr = (float) str_replace(',', '', (string) $request->debit_amount);
            $amountCr = (float) str_replace(',', '', (string) $request->credit_amount);

            $existing = SysChartofAccountsTransaction::where('company_id', $com_id)
                ->where('account_id', $accountId)
                ->where('transaction_type', 'openingbalance')
                ->where('status', 1)
                ->first();

            $transactionDate = $existing && !empty($existing->transaction_date)
                ? $existing->transaction_date
                : Carbon::createFromFormat('d/m/Y', $this->getCompanyOpeningBalanceDateDmy())->format('Y-m-d');

            if ($existing) {
                $existing->debit_amount = $amountDr;
                $existing->credit_amount = $amountCr;
                $existing->transaction_date = $transactionDate;
                $existing->updated_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                $existing->save();
            } else {
                SysHelper::trn_chartof_accounts_transaction(
                    $accountId,
                    $accountId,
                    'OPB-' . $accountId,
                    $transactionDate,
                    'openingbalance',
                    $amountDr,
                    $amountCr,
                    'Opening balance b/d',
                    1,
                    0,
                    "",
                    0
                );
            }

            DB::commit();

            return response()->json(['message' => 'Opening balance updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Unable to update opening balance.'], 500);
        }
    }

    public function chartofaccounts_opening_balance(Request $request)
    {
        try {
            $com_id = session('logged_session_data.company_id');

            $accountslist = SysChartofAccounts::select('main_account_id')->where('main_account_id', '!=', 0)->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->pluck('main_account_id');

            $account = SysChartofAccountsTransaction::select('account_id', 'account_code', 'transaction_date', 'debit_amount', 'credit_amount')
                ->join('sys_chartofaccounts', 'sys_chartofaccounts.id', 'sys_chartofaccounts_transaction.account_id')
                ->where('transaction_type', 'openingbalance')
                //->where(function ($query) {
                // $query->orwhere('sys_chartofaccounts.account_code','like','SUP%')
                // ->orwhere('sys_chartofaccounts.account_code','like','CUS%');
                //})
                ->wherenotin('sys_chartofaccounts.id', $accountslist)->where('sys_chartofaccounts_transaction.status', 1)
                ->where('sys_chartofaccounts_transaction.company_id', $com_id)->orderby('account_code', 'asc')->orderby('transaction_date', 'desc')->get();

            //$invoice = SysChartofaccountsOpeningBalanceInvoice::where('company_id',$com_id)->get();
            $invoice = SysChartofAccountsTransaction::where('company_id', $com_id)->where('transaction_type', 'opbinvoice')->get();

            return view('backEnd.chart-of-accounts.chartofaccountsopeningbalance', compact('account', 'invoice'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function chartofaccounts_opening_balance_edit($id)
    {
        try {

            $com_id = session('logged_session_data.company_id');

            $accountslist = SysChartofAccounts::select('main_account_id')->where('main_account_id', '!=', 0)->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->pluck('main_account_id');

            $account = SysChartofAccountsTransaction::select('account_id', 'account_code', 'transaction_date', 'debit_amount', 'credit_amount')
                ->join('sys_chartofaccounts', 'sys_chartofaccounts.id', 'sys_chartofaccounts_transaction.account_id')
                ->where('transaction_type', 'openingbalance')
                ->wherenotin('sys_chartofaccounts.id', $accountslist)->where('sys_chartofaccounts_transaction.status', 1)
                ->where('sys_chartofaccounts_transaction.company_id', $com_id)->orderby('account_code', 'asc')->orderby('transaction_date', 'desc')->get();

            $account_edit = SysChartofAccountsTransaction::select('account_id', 'account_code', 'transaction_date', 'debit_amount', 'credit_amount')
                ->join('sys_chartofaccounts', 'sys_chartofaccounts.id', 'sys_chartofaccounts_transaction.account_id')
                ->where('transaction_type', 'openingbalance')
                ->where('sys_chartofaccounts.id', $id)->where('sys_chartofaccounts_transaction.status', 1)
                ->where('sys_chartofaccounts_transaction.company_id', $com_id)->orderby('account_code', 'asc')->orderby('transaction_date', 'desc')->get();

            //$invoice = SysChartofaccountsOpeningBalanceInvoice::where('company_id',$com_id)->get();
            $invoice = SysChartofAccountsTransaction::where('company_id', $com_id)->where('account_id', $id)->where('transaction_type', 'opbinvoice')->get();


            $receiptAdjustments = SysReceiptAdjustments::where('bi_doc_no', $invoice->pluck('transaction_no'))->where('status', 1)->get();
            $returnAdjustments = SysSalesReturnAdjestment::where('siv_no', $invoice->pluck('transaction_no'))->where('status', 1)->get();


            return view('backEnd.chart-of-accounts.chartofaccountsopeningbalance_edit', compact('account', 'invoice', 'account_edit', 'receiptAdjustments', 'returnAdjustments'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function chartofaccounts_import_invoice(Request $request)
    {
        try {
            $com_id = session('logged_session_data.company_id');
            $data = DB::table('sys_chartofaccounts_opening_balance_invoice_import as i')->select('i.*')->where('i.status', 1)->where('i.company_id', session('logged_session_data.company_id'))->get();
            $account_name = DB::table('sys_chartofaccounts')->select('id', 'account_code')
                ->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->get();

            return view('backEnd.chart-of-accounts.importchartofaccounts_invoice', compact('data', 'account_name'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function chartofaccounts_import_invoice_list(Request $request)
    {
        try {
            DB::beginTransaction();
            $selected_file = "";
            if ($request->file('import_file') != "") {
                $file = $request->file('import_file');
                $selected_file = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/product_upload/', $selected_file);
                $selected_file = 'public/uploads/product_upload/' . $selected_file;
                //return  $selected_file;
            }

            $objPHPExcel = PHPExcel_IOFactory::load($selected_file);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow();

            $dataArray = $objPHPExcel->getActiveSheet()->toArray();
            //return count($dataArray[0]);
            /*->rangeToArray(
            'A1:C4',     // The worksheet range that we want to retrieve
            NULL,        // Value that should be returned for empty cells
            TRUE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            TRUE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            TRUE         // Should the array be indexed by cell row and cell column
            );*/

            for ($i = 1; $i < count($dataArray); $i++) {

                $bill_no = "";
                $bill_date = null;
                $sales_person = "";

                if (isset($dataArray[$i][10])) {
                    $bill_no = $dataArray[$i][10];
                }
                if (isset($dataArray[$i][11])) {
                    $bill_date = SysHelper::normalizeToYmd($dataArray[$i][11]);
                }
                if (isset($dataArray[$i][12])) {
                    $sales_person = $dataArray[$i][12];
                }
                //for($j=0; $j < count($dataArray[0]); $j++){
                $data[] = [
                    'invoice_date' => SysHelper::normalizeToYmd($dataArray[$i][0]),
                    'invoice_no' => $dataArray[$i][1],
                    'account_code' => $dataArray[$i][2],
                    'account_name' => $dataArray[$i][3],
                    'debit_amount' => $dataArray[$i][4],
                    'credit_amount' => $dataArray[$i][5],
                    'po_no' => $dataArray[$i][6],
                    'payment_terms' => $dataArray[$i][7],
                    'due_date' => SysHelper::normalizeToYmd($dataArray[$i][8]),
                    'deal_id' => $dataArray[$i][9],
                    'bill_no' => $bill_no,
                    'bill_date' => $bill_date,
                    'sales_person' => $sales_person,
                    'status' => 1,
                    'created_by' => Auth()->user()->id,
                    'company_id' => session('logged_session_data.company_id'),

                ];
                //}
                //$data2[]=$data;

            }

            SysChartofaccountsOpeningBalanceInvoiceImport::insert($data);
            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function chartofaccounts_import_invoice_clear(Request $request)
    {
        try {
            SysChartofaccountsOpeningBalanceInvoiceImport::where('company_id', session('logged_session_data.company_id'))->delete();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function chartofaccounts_import_invoice_data(Request $request)
    {
        try {
            DB::beginTransaction();
            $com_id = session('logged_session_data.company_id');
            $data = DB::table('sys_chartofaccounts_opening_balance_invoice_import as i')->select('i.*')->where('i.status', 1)->where('i.company_id', session('logged_session_data.company_id'))->get();
            $account_name = DB::table('sys_chartofaccounts')->select('id', 'account_code')
                ->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->get();

            $import_date = Carbon::now('+04:00')->format('Y-m-d H:i:s');

            if (count($data) > 0) {
                foreach ($data as $dt) {
                    $account_id = $account_name->where('account_code', $dt->account_code)->max('id');
                    $accounts = new SysChartofAccountsTransaction();
                    $accounts->account_id = $account_id;
                    $accounts->transaction_id = $account_id;
                    $accounts->transaction_no = $dt->invoice_no;
                    $accounts->transaction_date = $dt->invoice_date;
                    $accounts->transaction_type = 'opbinvoice';
                    $accounts->debit_amount = $dt->debit_amount;
                    $accounts->credit_amount = $dt->credit_amount;
                    $accounts->remarks = 'Invoive Opening Balance';
                    $accounts->status = 1;
                    $accounts->plan = 0;
                    $accounts->created_by = Auth::user()->id;
                    $accounts->created_at = Carbon::now('+04:00');
                    $accounts->company_id = session('logged_session_data.company_id');
                    $accounts->transaction_ref = "";
                    $accounts->entry_no = 1;
                    $accounts->save();
                    DB::table('sys_chartofaccounts_transaction_invoice_detail')->insert([
                        'trn_id' => $accounts->id,
                        'po_no' => $dt->po_no,
                        'payment_terms' => $dt->payment_terms,
                        'due_date' => $dt->due_date,
                        'deal_id' => $dt->deal_id,
                        'account_id' => $accounts->account_id,
                        'transaction_no' => $accounts->transaction_no,
                        'bill_no' => $dt->bill_no,
                        'bill_date' => $dt->bill_date,
                        'bill_date' => $dt->bill_date,
                        'sales_person' => $dt->sales_person,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                    ]);
                    // $accounts = new SysChartofaccountsOpeningBalanceInvoice();
                    // $accounts->account_id = $account_id;
                    // $accounts->account_code = $dt->account_code;
                    // $accounts->account_name = $dt->account_name;
                    // $accounts->invoice_date = $dt->invoice_date;
                    // $accounts->invoice_no = $dt->invoice_no;
                    // $accounts->debit_amount = $dt->debit_amount;
                    // $accounts->credit_amount = $dt->credit_amount;
                    // $accounts->status = 1;
                    // $accounts->company_id = $dt->company_id;
                    // $accounts->created_by = Auth::user()->id;
                    // $accounts->created_at = $import_date;
                    // $accounts->save();
                    // $accounts->toArray();
                }

                SysChartofaccountsOpeningBalanceInvoiceImport::where('company_id', session('logged_session_data.company_id'))->delete();
                DB::commit();
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
            Toastr::success('Invoice Imported Successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function chartofaccounts_invoice_delete(Request $request)
    {
        try {
            SysChartofAccountsTransaction::where('id', $request->id)->delete();

            //$retData = SysChartofaccountsOpeningBalanceInvoice::where('account_id',$request->account_id)->get();
            $retData = SysChartofAccountsTransaction::where('account_id', $request->account_id)->where('transaction_type', 'opbinvoice')->get();

            $bug = 0;
        } catch (\Throwable $e) {
            return $e;
            $bug = $e->errorInfo[1];
        }
        if ($bug == 0) {
            $retData = "OK";
            return json_encode(array('data' => $retData));
        } else {
            $retData = 'ERROR';
            return json_encode(array('data' => $retData));
        }
    }
    public function chartofaccounts_invoice_update(Request $request)
    {
        try {
            SysChartofAccountsTransaction::where('id', $request->id)->update([
                'transaction_date' => Carbon::createFromFormat('d/m/Y', $request->invoice_date)
                    ->format('Y-m-d'),
                'transaction_no' => $request->invoice_no,
                'debit_amount' => $request->debit_amount,
                'credit_amount' => $request->credit_amount,
            ]);

            $retData = SysChartofAccountsTransaction::where('account_id', $request->account_id)->where('transaction_type', 'opbinvoice')->get();

            $bug = 0;
        } catch (\Throwable $e) {
            return $e;
            $bug = $e->errorInfo[1];
        }
        if ($bug == 0) {
            $retData = "OK";
            return json_encode(array('data' => $retData));
        } else {
            $retData = 'ERROR';
            return json_encode(array('data' => $retData));
        }
    }

    function accountMerge(Request $request)
    {
        try {
            DB::beginTransaction();

            $from_account = $request->from_account;
            $to_account = $request->to_account;

            if (count($from_account) > 0) {
                foreach ($from_account as $f_account) {

                    $from_acc = DB::table('sys_chartofaccounts')->where('id', $from_account)->get();
                    $to_acc = DB::table('sys_chartofaccounts')->where('id', $to_account)->get();
                    $to_acc_check = DB::table('sys_chartofaccounts')->where('account_code', $to_acc[0]->account_code)->where('company_id', session('logged_session_data.company_id'))->get();

                    if (count($to_acc_check) > 1) {
                        DB::rollBack();
                        Toastr::error('Operation Failed. Multipple Account Code', 'Failed');
                        return redirect()->back();
                    }


                    DB::table('sys_item_stock')->where('account_id', $f_account)->update(['account_id' => $to_account]);
                    DB::table('sys_chartofaccounts_transaction')->where('account_id', $f_account)->update(['account_id' => $to_account]);

                    DB::table('sys_proforma_invoice')->where('customer', $f_account)->update(['customer' => $to_account]);
                    DB::table('sys_sales_invoice')->where('customer', $f_account)->update(['customer' => $to_account]);
                    DB::table('sys_delivery_note')->where('customer_id', $f_account)->update(['customer_id' => $to_account]);
                    DB::table('sys_sales_return')->where('customer', $f_account)->update(['customer' => $to_account]);
                    DB::table('sys_receipt_adjustments')->where('account_id', $f_account)->update(['account_id' => $to_account]);

                    DB::table('sys_purchase_order')->where('vendors', $f_account)->update(['vendors' => $to_account]);
                    DB::table('sys_purchase_grn')->where('vendors', $f_account)->update(['vendors' => $to_account]);
                    DB::table('sys_purchase_invoice')->where('vendors', $f_account)->update(['vendors' => $to_account]);
                    DB::table('sys_purchase_return')->where('vendors', $f_account)->update(['vendors' => $to_account]);
                    DB::table('sys_payment_adjustments')->where('account_id', $f_account)->update(['account_id' => $to_account]);


                    DB::table('sys_chartofaccounts')->where('id', $f_account)->update(['status' => 2]);

                    $str = $from_acc[0]->company_access;
                    $exploded = explode(',', $str);
                    $unique = array_unique($exploded);
                    $company_access = implode(',', $unique);

                    DB::table('sys_chartofaccounts')->where('id', $to_account)->update(['company_access' => $company_access]);
                }
            }
            DB::commit();
            Toastr::success('Account Merged Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }




    function subAccountMerge(Request $request)
    {
        try {
            DB::beginTransaction();

            $from_account = $request->from_account;
            $to_account = $request->to_account;

            if (count($from_account) > 0) {
                foreach ($from_account as $f_account) {

                    $from_acc = DB::table('sys_chartofaccounts')->where('id', $from_account)->get();
                    $to_acc = DB::table('sys_chartofaccounts')->where('id', $to_account)->get();
                    $to_acc_check = DB::table('sys_chartofaccounts')->where('account_code', $to_acc[0]->account_code)->where('company_id', session('logged_session_data.company_id'))->get();

                    if (count($to_acc_check) > 1) {
                        DB::rollBack();
                        Toastr::error('Operation Failed. Multipple Sub Account Code', 'Failed');
                        return redirect()->back();
                    }


                    DB::table('sys_item_stock')->where('account_id', $f_account)->update(['account_id' => $to_account]);
                    DB::table('sys_chartofaccounts_transaction')->where('account_id', $f_account)->update(['account_id' => $to_account]);

                    DB::table('sys_proforma_invoice')->where('customer', $f_account)->update(['customer' => $to_account]);
                    DB::table('sys_sales_invoice')->where('customer', $f_account)->update(['customer' => $to_account]);
                    DB::table('sys_delivery_note')->where('customer_id', $f_account)->update(['customer_id' => $to_account]);
                    DB::table('sys_sales_return')->where('customer', $f_account)->update(['customer' => $to_account]);
                    DB::table('sys_receipt_adjustments')->where('account_id', $f_account)->update(['account_id' => $to_account]);

                    DB::table('sys_purchase_order')->where('vendors', $f_account)->update(['vendors' => $to_account]);
                    DB::table('sys_purchase_grn')->where('vendors', $f_account)->update(['vendors' => $to_account]);
                    DB::table('sys_purchase_invoice')->where('vendors', $f_account)->update(['vendors' => $to_account]);
                    DB::table('sys_purchase_return')->where('vendors', $f_account)->update(['vendors' => $to_account]);
                    DB::table('sys_payment_adjustments')->where('account_id', $f_account)->update(['account_id' => $to_account]);


                    DB::table('sys_chartofaccounts')->where('id', $f_account)->update(['status' => 2]);

                    $str = $from_acc[0]->company_access;
                    $exploded = explode(',', $str);
                    $unique = array_unique($exploded);
                    $company_access = implode(',', $unique);

                    DB::table('sys_chartofaccounts')->where('id', $to_account)->update(['company_access' => $company_access]);
                }
            }
            DB::commit();
            Toastr::success('Sub Account Merged Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function account_name(Request $request)
    {
        try {
            if ($request->get('query')) {
                $account_name = $request->get('query');

                $com_id = session('logged_session_data.company_id');
                $data = DB::table('sys_chartofaccounts')->select('account_name')->whereRaw("find_in_set($com_id,company_access)")
                    ->where(function ($query) use ($account_name) {
                        $query->orwhere('account_name', 'like', '%' . $account_name . '%')
                            ->orwhere('account_name', 'like', '%' . str_replace(',', '', $account_name) . '%')
                            ->orwhere('account_name', 'like', '%' . str_replace(',', ' ', $account_name) . '%')
                            ->orwhere('account_name', 'like', '%' . str_replace('.', '', $account_name) . '%')
                            ->orwhere('account_name', 'like', '%' . str_replace('.', ' ', $account_name) . '%');
                    })->get();

                $output = '<ul class="form-control" style="list-style: none; height: auto; position: absolute; z-index: 999; line-height: 25px;">';
                foreach ($data as $row) {
                    $output .= '<li><a href="#">' . $row->account_name . '</a></li>';
                }
                $output .= '</ul>';
                echo $output;
            }
        } catch (\Throwable $th) {
            //return $th;
        }
    }

    public function get_account_list_ajax(Request $request)
    {
        try {
            $com_id = session('logged_session_data.company_id');
            $search = $request->search_text;

            $settings = SysHelper::getCompanyCodeSettings($com_id);

            $accounts = SysChartofAccounts::select('id', 'account_name', 'group', 'account_code')
                ->where('status', 1)
                ->whereNotIn('id', function ($query) use ($com_id) {
                    $query->select('main_account_id')
                        ->from('sys_chartofaccounts')
                        ->where('main_account_id', '!=', 0)
                        ->whereRaw("find_in_set(?, company_access)", [$com_id]);
                })
                ->whereRaw("find_in_set(?, company_access)", [$com_id])
                ->where(function ($query) use ($search) {
                    $query->where('account_name', 'like', '%' . $search . '%')
                        ->orWhere('account_code', 'like', '%' . $search . '%');
                })
                ->orderBy('account_name', 'asc')
                ->limit(50)
                ->get();

            // 🟢 Hide codes based on settings
            $accounts->transform(function ($item) use ($settings) {

                if (!$settings['is_account_code'] && Str::startsWith($item->account_code, 'ACC')) {
                    $item->account_code = null;
                }

                if (!$settings['is_subaccount_code'] && Str::startsWith($item->account_code, 'SACC')) {
                    $item->account_code = null;
                }

                if (!$settings['is_customer_code'] && Str::startsWith($item->account_code, 'CUS')) {
                    $item->account_code = null;
                }

                if (!$settings['is_supplier_code'] && Str::startsWith($item->account_code, 'SUP')) { // 🧠 changed SUPS → SUP
                    $item->account_code = null;
                }



                return $item;
            });

            return response()->json($accounts);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function get_cust_account_list_ajax(Request $request)
    {
        try {
            $com_id = session('logged_session_data.company_id');
            $search = $request->search_text;
            $customer = SysChartofAccounts::select('sys_chartofaccounts.id', 'sys_chartofaccounts.account_name', 'sys_chartofaccounts.account_code')
                ->where('sys_chartofaccounts.subgroup2', 7)
                ->where('sys_chartofaccounts.status', 1)
                ->whereRaw("find_in_set(?, sys_chartofaccounts.company_access)", [$com_id])
                ->where(function ($query) use ($search) {
                    $query->where('account_name', 'like', '%' . $search . '%')
                        ->orWhere('account_code', 'like', '%' . $search . '%');
                })
                ->orderBy('sys_chartofaccounts.account_name', 'asc')
                ->limit(50)
                ->get();


            $settings = SysHelper::getCompanyCodeSettings($com_id);

            // 🟢 Hide codes based on settings
            $customer->transform(function ($item) use ($settings) {

                if (!$settings['is_account_code'] && Str::startsWith($item->account_code, 'ACC')) {
                    $item->account_code = null;
                }

                if (!$settings['is_subaccount_code'] && Str::startsWith($item->account_code, 'SACC')) {
                    $item->account_code = null;
                }

                if (!$settings['is_customer_code'] && Str::startsWith($item->account_code, 'CUS')) {
                    //$item->account_code = null;
                }

                if (!$settings['is_supplier_code'] && Str::startsWith($item->account_code, 'SUP')) { // 🧠 changed SUPS → SUP
                    $item->account_code = null;
                }

                return $item;
            });

            return response()->json($customer);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function get_supp_account_list_ajax(Request $request)
    {
        try {
            $com_id = session('logged_session_data.company_id');
            $search = $request->search_text;
            $customer = SysChartofAccounts::select('sys_chartofaccounts.id', 'sys_chartofaccounts.account_name', 'sys_chartofaccounts.account_code')
                ->where('sys_chartofaccounts.subgroup2', 19)
                ->where('sys_chartofaccounts.status', 1)
                ->whereRaw("find_in_set(?, sys_chartofaccounts.company_access)", [$com_id])
                ->where(function ($query) use ($search) {
                    $query->where('account_name', 'like', '%' . $search . '%')
                        ->orWhere('account_code', 'like', '%' . $search . '%');
                })
                ->orderBy('sys_chartofaccounts.account_name', 'asc')
                ->limit(50)
                ->get();

            return response()->json($customer);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }




    // Opening Balance Start

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

    public function loadAccountModal(Request $request)
    {
        $com_id = session('logged_session_data.company_id');

        // Use cache to store the data for 5 minutes per company
        $cacheKey = "account_modal_data_{$com_id}";

        $data = cache()->remember($cacheKey, 300, function () use ($com_id) {
            // Select only required columns to reduce memory usage and improve speed
            $accounts2 = SysChartofAccounts::select(
                'id',
                'account_code',
                'account_name',
                'group',
                'subgroup',
                'subgroup2',
                'main_account_id',
                'status',
                'beneficiary_name',
                'bank_name',
                'acc_no',
                'iban',
                'swift_code',
                'routing_code',
                'branch',
                'branch_location',
                'stl_dept',
                'stl',
                'stl_limit'
            )

                ->whereRaw("find_in_set($com_id, sys_chartofaccounts.company_access)")
                ->orderBy('group', 'asc')
                ->orderBy('subgroup', 'asc')
                ->orderBy('subgroup2', 'asc')
                ->get();

            // Get transactions and index by account_id for O(1) lookups instead of O(n)
            $account_tran2 = SysChartofAccountsTransaction::select(
                'account_id',
                'transaction_date',
                'debit_amount',
                'credit_amount'
            )
                ->where('company_id', $com_id)
                ->where('transaction_type', 'openingbalance')
                ->get()
                ->keyBy('account_id'); // Index by account_id for faster lookups

            // Select only needed columns for account groups
            $accountgroupsub2 = SysAccountGroupSub2::select(
                'id',
                'title',
                'group_id',
                'sub_id',
                'status'
            )
                ->where('status', 1)
                ->orderBy('group_id', 'asc')
                ->get();

            return [
                'accounts2' => $accounts2,
                'account_tran2' => $account_tran2,
                'accountgroupsub2' => $accountgroupsub2
            ];
        });

        return view('backEnd.chart-of-accounts.accounts-table-view', $data);
    }

    public function loadAccountModalPaginated(Request $request)
    {
        $com_id = session('logged_session_data.company_id');
        $page = $request->input('page', 1);
        $perPage = 50; // Load 50 records at a time

        // Get paginated accounts
        $accounts2 = SysChartofAccounts::select(
            'id',
            'account_code',
            'account_name',
            'group',
            'subgroup',
            'subgroup2',
            'main_account_id',
            'status'
        )
            ->where('main_account_id', 0)
            ->whereRaw("find_in_set($com_id, sys_chartofaccounts.company_access)")
            ->orderByRaw('CASE WHEN status = 2 THEN 1 ELSE 0 END ASC')
            ->orderBy('group', 'asc')
            ->orderBy('subgroup', 'asc')
            ->orderBy('subgroup2', 'asc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Get transactions for these accounts
        $accountIds = $accounts2->pluck('id')->toArray();
        $account_tran2 = SysChartofAccountsTransaction::select(
            'account_id',
            'debit_amount',
            'credit_amount'
        )
            ->where('company_id', $com_id)
            ->where('transaction_type', 'openingbalance')
            ->whereIn('account_id', $accountIds)
            ->get()
            ->keyBy('account_id');

        // Build HTML rows
        $html = '';
        foreach ($accounts2 as $value) {
            $tran_amt = $account_tran2->get($value->id);
            $statusClass = $value->status == 2 ? 'bg-dark' : '';
            $statusText = $value->status == 1 ? '<span class="text-success">Active</span>' : '<span class="text-danger">Deleted</span>';

            $debit = $tran_amt ? \App\SysHelper::com_curr_format($tran_amt->debit_amount, '', '', ',') : '0.00';
            $credit = $tran_amt ? \App\SysHelper::com_curr_format($tran_amt->credit_amount, '', '', ',') : '0.00';

            $html .= '<tr class="' . $statusClass . '">';
            $html .= '<td class="text-center" style="padding-left: 14px">' . e($value->account_code) . '</td>';
            $html .= '<td>' . e($value->account_name) . '</td>';
            $html .= '<td>' . e($value->groupname->title ?? '') . '</td>';
            $html .= '<td>' . e($value->subgroupname->title ?? '') . '</td>';
            $html .= '<td>' . e($value->subgroup2name->title ?? '') . '</td>';
            $html .= '<td class="text-end">' . $debit . '</td>';
            $html .= '<td class="text-end">' . $credit . '</td>';
            $html .= '<td class="text-center">' . $statusText . '</td>';
            $html .= '<td class="text-center"><div class="d-flex justify-content-center align-items-center">';

            if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2 || auth()->user()->role_id == 27) {
                $html .= '<a class="btn-sm btn btn-light editAccountBtn2" data-id="' . $value->id . '" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Edit Account"
                            data-bs-placement="top"><i style="font-size: 16px" class="ico icon-outline-pen-2"></i></a>';

                if ($value->status == 2) {
                    $html .= '<a class="btn-sm btn btn-light" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Restore Account"
                            data-bs-placement="top" href="' . url('chartofaccounts/' . $value->id . '/restore') . '" onclick="return confirm(\'Are you sure you want to restore this item?\');"><i class="ico icon-bold-restart text-dark" style="font-size: 16px;"></i></a>';
                } else {
                    $html .= '<a class="btn-sm btn btn-light" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Delete Account"
                            data-bs-placement="top" href="' . url('chartofaccounts/' . $value->id . '/delete') . '" onclick="return confirm(\'Are you sure you want to delete this item?\');"><i style="font-size: 16px" class="ico icon-outline-trash-bin-minimalistic"></i></a>';
                }

                if ($value->main_account_id == 0) {
                    if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2) {
                        $html .= '<a class="btn-sm btn btn-light text-dark moveToSubGroupBtn" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Move to Sub Group" data-id="' . $value->id . '" data-name="' . addslashes($value->account_name) . '"
                            data-bs-placement="top"><i class="ico icon-outline-move-to-folder" style="font-size: 16px"></i>Sub Group</a>';
                        $html .= '<a class="btn-sm btn btn-light text-dark" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Move to Sub Account"
                            data-bs-placement="top" style="cursor: pointer;" onclick="move_sub_account2(' . $value->id . ',\'' . addslashes($value->account_name) . '\')"><i class="ico icon-outline-move-to-folder" style="font-size: 16px"></i> Sub Acc</a>';
                    }
                }
            }

            $html .= '</div></td>';
            $html .= '</tr>';
        }

        return response()->json([
            'html' => $html,
            'hasMore' => count($accounts2) == $perPage
        ]);
    }

    function accountMove(Request $request)
    {
        try {
            DB::beginTransaction();




            if ($request->move_to == 'other_account') {

                SysChartofAccounts::where('id', $request->from_account)->update([
                    'account_code' => SysHelper::get_new_sub_account_code(),
                    'main_account_id' => $request->sub_account,
                ]);
                Toastr::success('Main Account Moved to Sub Account', 'Success');


            } else {
                $check_tran = SysChartofAccountsTransaction::where('account_id', $request->from_account)->get();
                $check_tran2 = SysItemStock::where('account_id', $request->from_account)->get();

                if (count($check_tran) > 0) {
                    Toastr::warning('This account has some transactions. Please remove those transactions.', 'Warning');
                    return redirect()->back();
                }
                if (count($check_tran2) > 0) {
                    Toastr::warning('This account has some transactions. Please remove those transactions.', 'Warning');
                    return redirect()->back();
                }
                $data = SysChartofAccounts::find($request->from_account);
                $groupid = SysAccountGroupSub::select('group_id')->where('id', $request->group_account)->first();


                $accountgroup2 = new SysAccountGroupSub2();
                $accountgroup2->group_id = $groupid->group_id;
                $accountgroup2->sub_id = $request->group_account;
                $accountgroup2->title = $data->account_name;
                $accountgroup2->status = 1;
                $accountgroup2->created_by = Auth::user()->id;
                $results = $accountgroup2->save();

                $check_sub = SysChartofAccounts::where('main_account_id', $request->from_account)->get();
                if (count($check_sub)) {
                    foreach ($check_sub as $dt) {
                        SysChartofAccounts::where('id', $dt->id)->update([
                            'account_code' => SysHelper::get_new_account_code(),
                            'main_account_id' => 0,
                            'subgroup2' => $accountgroup2->id,
                        ]);
                    }
                }

                SysChartofAccounts::where('id', $request->from_account)->delete();
                Toastr::success('Main Account Moved to Sub Group', 'Success');

            }


            DB::commit();

            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function subAccountMove(Request $request)
    {
        try {
            DB::beginTransaction();

            $groups = SysAccountGroupSub2::select('group_id', 'sub_id')->where('id', $request->subgroup_account)->first();

            SysChartofAccounts::where('id', $request->from_subaccount)->update([
                'account_code' => SysHelper::get_new_account_code(),
                'main_account_id' => 0,
                'group' => $groups->group_id,
                'subgroup' => $groups->sub_id,
                'subgroup2' => $request->subgroup_account,
            ]);
            Toastr::success('Sub Account Moved to Account', 'Success');


            DB::commit();

            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

}
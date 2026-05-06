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

class SysChartofAccountsController extends Controller
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
            $accountgroup = SysAccountGroup::where('status', 1)->get();
            $account_sub = SysChartofAccounts::select('id', 'account_code', 'account_name', 'main_account_id', 'group', 'subgroup', 'subgroup2','status')->where('main_account_id', '!=', 0)->orderby('account_name','asc')->get();
            //$accounts = SysChartofAccounts::whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)");
            //where('company_id',$company_id)->get();
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

        $results = SysChartofAccounts::whereRaw("find_in_set(?, company_access)", [$com_id])
                    ->where(function($query) use ($q) {
                        $query->where('account_name', 'like', "%{$q}%")
                            ->orWhere('account_code', 'like', "%{$q}%");
                    })
                    ->where('main_account_id', '!=', 0)
                    ->take(20)
                    ->get();

        return view('backEnd.chart-of-accounts.chartofaccount_search', compact('results'));
    }

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
            $accounts = SysChartofAccounts::whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->get();
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
            $amount_dr = $request->debit_amount;
            $amount_cr = $request->credit_amount;

            if (count($valid) == 0) {
                $accounts = new SysChartofAccounts();
                $accounts->account_code = SysHelper::get_new_account_code();
                $accounts->account_name = $request->account_name;
                $groups = SysAccountGroupSub2::select('group_id', 'sub_id')->where('id', $request->subgroup2)->first();
                $accounts->group = $groups->group_id;
                $accounts->subgroup = $groups->sub_id;
                $accounts->subgroup2 = $request->subgroup2;
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

                SysHelper::trn_chartof_accounts_transaction($accounts->id, $accounts->id, 'OPB-' . $accounts->id, Carbon::createFromFormat('d/m/Y', $request->opening_balance_date)->format('Y-m-d'), 'openingbalance', $amount_dr, $amount_cr, 'Opening balance b/d', 1, 0, "", 0);

                DB::commit();
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

    public function move(Request $request, $id)
    {
        try {
            db::beginTransaction();
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
            $data = SysChartofAccounts::find($id);
            $accountgroup2 = new SysAccountGroupSub2();
            $accountgroup2->group_id = $data->group;
            $accountgroup2->sub_id = $data->subgroup;
            $accountgroup2->title = $data->account_name;
            $accountgroup2->status = 1;
            $accountgroup2->created_by = Auth::user()->id;
            $results = $accountgroup2->save();

            $check_sub = SysChartofAccounts::where('main_account_id', $id)->get();
            if (count($check_sub)) {
                foreach ($check_sub as $dt) {
                    SysChartofAccounts::where('id', $dt->id)->update([
                        'account_code' => SysHelper::get_new_account_code(),
                        'main_account_id' => 0,
                        'subgroup2' => $accountgroup2->id,
                    ]);
                }
            }

            SysChartofAccounts::where('id', $id)->delete();

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
                return redirect('chartofaccounts-add');

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


                $amount_dr = $request->debit_amount;
                $amount_cr = $request->credit_amount;

                $companyId = session('logged_session_data.company_id');

                $checkDataExists = DB::table('sys_chartofaccounts_transaction')
                    ->where('account_id', $id)
                    ->where('company_id', $companyId)
                    ->where('transaction_type', 'openingbalance')
                    ->where('status', 1)
                    ->get();

                if (count($checkDataExists) > 0) {
                    // Update existing opening balance transaction
                    DB::table('sys_chartofaccounts_transaction')
                        ->where('account_id', $id)
                        ->where('company_id', $companyId)
                        ->where('transaction_type', 'openingbalance')
                        ->update([
                            'debit_amount' => $amount_dr,
                            'credit_amount' => $amount_cr,
                            'transaction_date' => Carbon::createFromFormat('d/m/Y', $request->opening_balance_date)->format('Y-m-d'),
                        ]);
                } else {
                    // Insert new opening balance transaction
                    SysHelper::trn_chartof_accounts_transaction(
                        $id,
                        $id,
                        'OPB-' . $id,
                        Carbon::createFromFormat('d/m/Y', $request->opening_balance_date)->format('Y-m-d'),
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

                DB::commit();
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();

            } catch (\Exception $e) {
                DB::rollBack();
                return $e;
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        } else {
            DB::rollBack();
            Toastr::error('Oops!! something went wrong', 'Failed');
            return redirect()->back();
        }
    }

    public function delete($id)
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
    public function restore($id)
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

    function getchartofaccountsinfo(Request $request)
    {
        try {
            $ret = DB::table('sys_chartofaccounts as ca')
                ->select('ca.id', 'ca.account_code', 'ca.account_name', 'cs.address', 'cs.address2', 'cs.contcat_person', DB::raw("CONCAT(cs.customer_salutation, ' ', cs.first_name, ' ', cs.last_name) AS contact_person"), 'cs.contcat_number', 'cs.email', 'cs.payment_terms', 'cs.vat_country', 'cs.vat_state', 'cs.vat_type', 'cs.purchase_type', 'cs.customer_type', 'cs.sale_type', 'cs.supplier_type', 'cs.vat_percentage', 'cs.customer_salutation', 'cs.first_name', 'cs.last_name', DB::raw("CONCAT(addr.address, ' ', addr.address2, ' ', addr.city,' ',addr.zip_code) AS shipping_address"))
                ->leftjoin('sys_cust_suppl as cs', 'cs.code', 'ca.account_code')
                ->leftJoin('sys_cust_suppl_addressbook as addr', 'addr.cust_suppl_id', 'cs.id')
                ->where('ca.id', $request->id)->get();

            return json_encode(array('data' => $ret));

        } catch (\Exception $e) {
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
                    $dataArray[0][0] => $dataArray[$i][0],
                    $dataArray[0][1] => $dataArray[$i][1],
                    $dataArray[0][2] => $dataArray[$i][2],
                    $dataArray[0][3] => $dataArray[$i][3],
                    $dataArray[0][4] => $dataArray[$i][4],
                    $dataArray[0][5] => $dataArray[$i][5],
                    'created_by' => Auth()->user()->id,
                    'company_id' => session('logged_session_data.company_id'),

                ];
                //}
                //$data2[]=$data;

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

            $account_group = DB::table('sys_account_group')->select('id', 'title')->get();
            $account_group_sub = DB::table('sys_account_group_sub')->select('id', 'title')->get();
            $account_group_sub2 = DB::table('sys_account_group_sub2')->select('id', 'title')->get();

            $group_id = 0;
            $group_sub_id = 0;
            $group_sub2_id = 0;

            $company_access = session('logged_session_data.company_id');
            //if(session('logged_session_data.company_id') != 1){ $company_access = '1,'.session('logged_session_data.company_id'); }
            $import_date = Carbon::now('+04:00')->format('Y-m-d H:i:s');

            if (count($data) > 0) {
                foreach ($data as $dt) {
                    $group_id = $account_group->where('title', $dt->group)->max('id');
                    $group_sub_id = $account_group_sub->where('title', $dt->subgroup)->max('id');
                    $group_sub2_id = $account_group_sub2->where('title', $dt->subgroup2)->max('id');

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
                    $accounts->account_code = SysHelper::get_new_account_code();
                    $accounts->account_name = $dt->account_name;
                    $accounts->group = $group_id;
                    $accounts->subgroup = $group_sub_id;
                    $accounts->subgroup2 = $group_sub2_id;
                    $accounts->status = 1;
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
                    $dataArray[0][0] => $dataArray[$i][0],
                    $dataArray[0][1] => $dataArray[$i][1],
                    $dataArray[0][2] => $dataArray[$i][2],
                    $dataArray[0][3] => $dataArray[$i][3],
                    $dataArray[0][4] => $dataArray[$i][4],
                    $dataArray[0][5] => $dataArray[$i][5],
                    $dataArray[0][6] => $dataArray[$i][6],
                    $dataArray[0][7] => $dataArray[$i][7],
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

            $account_group = DB::table('sys_account_group')->select('id', 'title')->get();
            $account_group_sub = DB::table('sys_account_group_sub')->select('id', 'title')->get();
            $account_group_sub2 = DB::table('sys_account_group_sub2')->select('id', 'title')->get();

            $group_id = 0;
            $group_sub_id = 0;
            $group_sub2_id = 0;

            $company_access = session('logged_session_data.company_id');
            //if(session('logged_session_data.company_id') != 1){ $company_access = '1,'.session('logged_session_data.company_id'); }
            $import_date = Carbon::now('+04:00')->format('Y-m-d H:i:s');

            if (count($data) > 0) {
                foreach ($data as $dt) {
                    $main_account_id = $account_id->where('account_name', $dt->account_name)->max('id');
                    $group_id = $account_group->where('title', $dt->group)->max('id');
                    $group_sub_id = $account_group_sub->where('title', $dt->subgroup)->max('id');
                    $group_sub2_id = $account_group_sub2->where('title', $dt->subgroup2)->max('id');

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
                    $accounts->main_account_id = $main_account_id;
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

                    SysHelper::trn_chartof_accounts_transaction($accounts->id, $accounts->id, 'OPB-' . $accounts->id, date('Y-m-d', strtotime($dt->sub_account_date)), 'openingbalance', $amount_dr, $amount_cr, 'Opening balance b/d', 1, 0, "", 0);
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
            $sub_accounts = SysChartofAccounts::whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->where('main_account_id', '!=', 0)->get();
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


        session(['opening_balance_date_sub' => $request->opening_balance_date]);

        $input = $request->all();
        $valid = SysChartofAccounts::where(
            [
                'account_name' => $request->account_name,
                'main_account_id' => $request->main_account_id,
            ]
        )->get();
        try {
            $amount_dr = $request->debit_amount;
            $amount_cr = $request->credit_amount;

            if (count($valid) == 0) {
                $get_acc_det = SysChartofAccounts::where('id', $request->main_account_id)->first();
                $accounts = new SysChartofAccounts();
                $accounts->account_code = SysHelper::get_new_sub_account_code();
                $accounts->account_name = $request->account_name;
                $accounts->group = $get_acc_det->group;
                $accounts->subgroup = $get_acc_det->subgroup;
                $accounts->subgroup2 = $get_acc_det->subgroup2;
                $accounts->main_account_id = $request->main_account_id;
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

                SysHelper::trn_chartof_accounts_transaction($accounts->id, $accounts->id, 'OPB-' . $accounts->id,  Carbon::createFromFormat('d/m/Y', $request->opening_balance_date)->format('Y-m-d'), 'openingbalance', $amount_dr, $amount_cr, 'Opening balance b/d', 1, 0, "", 0);
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
            if (isset($request->account_id_emp)) {

            } else {
                Toastr::error('Operation Failed. Please Select Accounts', 'Failed');
                return redirect()->back();
            }
            DB::beginTransaction();
            foreach ($request->account_id_emp as $emp) {
                $det = SysHelper::get_account_details_for_employee_sub_add($com_id, $emp);
                if (isset($det)) {
                    if ($det == "no_data_found") {
                        DB::rollBack();
                        Toastr::error('Operation Failed. Accounts not found', 'Failed');
                        return redirect()->back();
                    }
                    $valid = SysChartofAccounts::where(['account_name' => ucwords($request->employee_name) . ' ' . $det->sub_account_name])->get();
                    if (count($valid) > 1) {
                        DB::rollBack();
                        Toastr::error('Accounts Name Already Excist', 'Failed');
                        return redirect()->back();
                    }

                    $accounts = new SysChartofAccounts();
                    $accounts->account_code = SysHelper::get_new_sub_account_code();
                    $accounts->account_name = ucwords($request->employee_name) . ' ' . $det->sub_account_name;
                    $accounts->group = $det->group;
                    $accounts->subgroup = $det->subgroup;
                    $accounts->subgroup2 = $det->subgroup2;
                    $accounts->main_account_id = $det->id;
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

    public function move_sub(Request $request, $id)
    {
        try {
            SysChartofAccounts::where('id', $id)->update([
                'account_code' => SysHelper::get_new_account_code(),
                'main_account_id' => 0,
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
                return redirect('chartofaccounts-add');

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
                //$accounts->company_id = session('logged_session_data.company_id');
                //$accounts->company_access = session('logged_session_data.company_id');
                $accounts->updated_by = Auth()->user()->id;
                $accounts->updated_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                $results = $accounts->update();
                //$accounts->account_type = $request->account_type;
                //$accounts->billwise = $request->billwise;
                //$accounts->debitlimit = $request->debitlimit;


                $amount_dr = $request->debit_amount;
                $amount_cr = $request->credit_amount;

                $check_data_excist = DB::table('sys_chartofaccounts_transaction')->where('account_id', $id)->where('company_id', session('logged_session_data.company_id'))->where('transaction_type', 'openingbalance')->count();
                if ($check_data_excist == 0) {
                    SysHelper::trn_chartof_accounts_transaction($id, $id, 'OPB-' . $id,  Carbon::createFromFormat('d/m/Y', $request->opening_balance_date)->format('Y-m-d'), 'openingbalance', $amount_dr, $amount_cr, 'Opening balance b/d', 1, 0, "", 0);
                } else {
                    DB::table('sys_chartofaccounts_transaction')->where('account_id', $id)->where('company_id', session('logged_session_data.company_id'))->where('transaction_type', 'openingbalance')->update(
                        [
                            'debit_amount' => $amount_dr,
                            'credit_amount' => $amount_cr,
                            'transaction_date' => Carbon::createFromFormat('d/m/Y', $request->opening_balance_date)->format('Y-m-d'),
                        ]
                    );
                }


                if ($results) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('chartofaccounts-add-sub');
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
                    $bill_date = date('Y-m-d', strtotime($dataArray[$i][11]));
                }
                if (isset($dataArray[$i][12])) {
                    $sales_person = $dataArray[$i][12];
                }
                //for($j=0; $j < count($dataArray[0]); $j++){
                $data[] = [
                    'invoice_date' => date('Y-m-d', strtotime($dataArray[$i][0])),
                    'invoice_no' => $dataArray[$i][1],
                    'account_code' => $dataArray[$i][2],
                    'account_name' => $dataArray[$i][3],
                    'debit_amount' => $dataArray[$i][4],
                    'credit_amount' => $dataArray[$i][5],
                    'po_no' => $dataArray[$i][6],
                    'payment_terms' => $dataArray[$i][7],
                    'due_date' => date('Y-m-d', strtotime($dataArray[$i][8])),
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
                    DB::table('sys_chartofaccounts_transaction_Invoice_detail')->insert([
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
                'transaction_date' =>Carbon::createFromFormat('d/m/Y', $request->invoice_date)
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
}
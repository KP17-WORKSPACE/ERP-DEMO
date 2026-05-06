<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use App\SysCustomer;
use App\SysCompany;
use App\SysPaymentTerms;
use App\SmBaseSetup;
use App\ApiBaseMethod;
use App\SmDesignation;
use App\SmLeaveRequest;
use App\SmHumanDepartment;
use App\SmStudentDocument;
use App\SmStudentTimeline;
use App\SmHrPayrollGenerate;
use App\SmStaff;
use App\SysAccountGroupSub2;
use App\SysAccountType;
use App\SysCustSuppl;
use App\SysChartofAccounts;
use App\SysChartofAccountsTransaction;
use App\SysCountries;
use App\SysCountryCode;
use App\SysCustomerType;
use App\SysCustSupplAddressbook;
use App\SysCustSupplAddressbookCart;
use App\SysCustSupplAddressbookForm;
use App\SysCustSupplContact;
use App\SysCustSupplContactForm;
use App\SysCustSupplDoc;
use App\SysCustSupplDocForm;
use App\SysCustSupplForm;
use App\SysCustSupplSTL;
use App\SysHelper;
use App\SysPurchaseType;
use App\SysStates;
use App\SysSupplierType;
use App\SysSupplImport;
use App\SysVat;
use App\SysVatType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPExcel;
use PHPExcel_IOFactory;
use App\SysSaleType;

class SysSupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }


    public function getSupplierData($id)
    {
        try {
            $custDetails = SysCustSuppl::find($id);
            $custAddress = SysCustSupplAddressbook::where('cust_suppl_id', $id)->get();
            $custContact = SysCustSupplContact::where('cust_suppl_id', $id)->get();
            $custDoc = SysCustSupplDoc::where('cust_suppl_id', $id)->get();

            $data_all = [];
            $amount = 0;
            $overdue = -999999;
            $ageing = -999999;

            $com_id = session('logged_session_data.company_id');
            $acc_id = SysChartofAccounts::where('account_code', $custDetails->code)->first();
            $account_id = $acc_id->id;
            $accounts = SysChartofAccounts::where('account_code', $custDetails->code)->get();
            $transaction_no = SysChartofAccountsTransaction::where('account_id', $account_id)->where('status', 1)->where('company_id', $com_id)->pluck('transaction_no');
            if (count($transaction_no) > 0) {
                $data_query = SysChartofAccountsTransaction::select('transaction_date', 'transaction_id', 'transaction_no', DB::raw('sum(debit_amount) as debit_amount'), DB::raw('sum(credit_amount) as credit_amount'), DB::raw($account_id . ' as account_id'))->where('company_id', $com_id);
                $data_query->wherein('transaction_no', $transaction_no)->where('status', 1);

                if ($account_id == 7642) {
                    $cash_supplier_list = SysHelper::get_cash_supplier($account_id);
                    if (count($cash_supplier_list) > 0) {
                        $data_query->wherein('transaction_no', $cash_supplier_list)->where('account_id', $account_id);
                    } else {
                        $data_query->where('transaction_no', '0')->where('account_id', $account_id);
                    }
                    $data_query->wherein('transaction_type', ['purchaseinvoice', 'purchasereturn', 'opbinvoice', 'openingbalance111', 'salesinvoice']);
                } else {
                    $data_query->wherein('transaction_type', ['purchaseinvoice', 'purchasereturn', 'opbinvoice', 'openingbalance111']);
                }
                $data_query->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '" . date('Y-m-d') . "'");
                $data_all[] = $data_query->groupby('transaction_date', 'transaction_id', 'transaction_no')->orderby('transaction_date', 'asc')->get();
            }


            if (!empty($custDetails)) {
                return compact('custDetails', 'custAddress', 'custContact', 'custDoc', 'data_all', 'accounts', 'amount', 'overdue', 'ageing');
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

    public function getSupplierDetails($id)
    {
        $data = $this->getSupplierData($id);

        if (!empty($data) && is_array($data)) {
            return view('backEnd.cust-suppl.viewSupplier', $data);
        } else {
            return response("Error loading details!", 404);
        }
    }


    // public function viewSupplier($id)
    // {
    //     try {
    //         $custDetails = SysCustSuppl::find($id);
    //         $custAddress = SysCustSupplAddressbook::where('cust_suppl_id', $id)->get();
    //         $custContact = SysCustSupplContact::where('cust_suppl_id', $id)->get();
    //         $custDoc = SysCustSupplDoc::where('cust_suppl_id', $id)->get();

    //         $data_all = [];
    //         $amount = 0;
    //         $overdue = -999999;
    //         $ageing = -999999;

    //         $com_id = session('logged_session_data.company_id');
    //         $acc_id = SysChartofAccounts::where('account_code', $custDetails->code)->first();
    //         $account_id = $acc_id->id;
    //         $accounts = SysChartofAccounts::where('account_code', $custDetails->code)->get();
    //         $transaction_no = SysChartofAccountsTransaction::where('account_id', $account_id)->where('status', 1)->where('company_id', $com_id)->pluck('transaction_no');
    //         if (count($transaction_no) > 0) {
    //             $data_query = SysChartofAccountsTransaction::select('transaction_date', 'transaction_id', 'transaction_no', DB::raw('sum(debit_amount) as debit_amount'), DB::raw('sum(credit_amount) as credit_amount'), DB::raw($account_id . ' as account_id'))->where('company_id', $com_id);
    //             $data_query->wherein('transaction_no', $transaction_no)->where('status', 1);

    //             if ($account_id == 7642) {
    //                 $cash_supplier_list = SysHelper::get_cash_supplier($account_id);
    //                 if (count($cash_supplier_list) > 0) {
    //                     $data_query->wherein('transaction_no', $cash_supplier_list)->where('account_id', $account_id);
    //                 } else {
    //                     $data_query->where('transaction_no', '0')->where('account_id', $account_id);
    //                 }
    //                 $data_query->wherein('transaction_type', ['purchaseinvoice', 'purchasereturn', 'opbinvoice', 'openingbalance111', 'salesinvoice']);
    //             } else {
    //                 $data_query->wherein('transaction_type', ['purchaseinvoice', 'purchasereturn', 'opbinvoice', 'openingbalance111']);
    //             }
    //             $data_query->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '" . date('Y-m-d') . "'");
    //             $data_all[] = $data_query->groupby('transaction_date', 'transaction_id', 'transaction_no')->orderby('transaction_date', 'asc')->get();
    //         }


    //         if (!empty($custDetails)) {
    //             return view('backEnd.cust-suppl.viewSupplier', compact('custDetails', 'custAddress', 'custContact', 'custDoc', 'data_all', 'accounts', 'amount', 'overdue', 'ageing'));
    //         } else {
    //             Toastr::error('Operation Failed', 'Failed');
    //             return redirect()->back();
    //         }
    //     } catch (\Exception $e) {
    //         return $e;
    //         Toastr::error('Operation Failed', 'Failed');
    //         return redirect()->back();
    //     }
    // }

    public function suppliers(Request $request, $id = null)
    {
        // SysSupplImport::
        // $customer = DB::table('sys_cust_suppl')->where('company_id',session('logged_session_data.company_id'))->where('catid',1)->pluck('name');

        // $data = DB::table('sys_suppl_import as i')->select('i.*')->where('i.status',1)->where('i.company_id',session('logged_session_data.company_id'))
        // ->whereNotIn('i.name',$customer)->get();

        // return  $data;

        try {

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $com_id = session('logged_session_data.company_id');

            $staff = SmStaff::select('user_id', 'full_name')->wherein('company_id', $company_id)->get();
            $supplier_list = SysHelper::get_supplier_list_all($com_id);

            $supplier_query = SysCustSuppl::where('catid', 2)   //where('catid',1)  - customer catid
                ->whereRaw("find_in_set($com_id,company_access)");


            $ctrl_vat = "";
            $ctrl_company_name = "";
            $ctrl_contact_name = "";
            $ctrl_email = "";
            $ctrl_sales_person = "";

            if (SysHelper::get_pagination_post($request)) {
                if ($request->company_name != "") {
                    $supplier_query->where('name', 'like', '%' . $request->company_name . '%');
                    $ctrl_company_name = $request->company_name;
                }
                if ($request->contact_name != "") {
                    $supplier_query->where('contcat_person', 'like', '%' . $request->contact_name . '%');
                    $ctrl_contact_name = $request->contact_name;
                }
                if ($request->email != "") {
                    $supplier_query->where('email', 'like', '%' . $request->email . '%');
                    $ctrl_email = $request->email;
                }
                if ($request->vat != "") {
                    $supplier_query->where('vat_number', 'like', '%' . $request->vat . '%');
                    $ctrl_vat = $request->vat;
                }
                if ($request->sales_person != "") {
                    $sales_person = DB::table('sys_cust_suppl_assign')->select('cust_supp_id')->where('user_id', $request->sales_person)->get();
                    if (count($sales_person) > 0) {
                        foreach ($sales_person as $spid) {
                            $sp[] = $spid->cust_supp_id;
                        }
                        $supplier_query->wherein('id', $sp);
                    } else {
                        $supplier_query->where('id', 0);
                    }
                }
            } else {

            }

            //$supplier_query->whereRaw("find_in_set($com_id,company_access)");
            //$query->wherein('created_by',$r[1]);
            //$supplier = $supplier_query->orderby('name','asc')->paginate(30);
            $supplier = $supplier_query->orderByRaw('status = 2')->orderby('id', 'desc')->get();

            $duplicateNames = DB::table('sys_chartofaccounts')
                ->select(DB::raw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(account_name)), ' ', ''), '@', ''), '#', ''), '.', ''), '-', ''), '_', ''), '(', ''), ')', '') AS duplicate_name"))->where('subgroup2', 19)->where('status', 1)
                ->groupBy(DB::raw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(account_name)), ' ', ''), '@', ''), '#', ''), '.', ''), '-', ''), '_', ''), '(', ''), ')', '')"))
                ->havingRaw('COUNT(*) > 1')
                ->get();

            if (count($duplicateNames) > 0) {
                $dn = [];
                foreach ($duplicateNames as $key) {
                    $dn[] = $key->duplicate_name;
                }
                $placeholders = implode(',', array_fill(0, count($dn), '?'));

                $duplicate_customer = DB::table('sys_chartofaccounts')
                    ->select('id', 'account_name', 'account_code', DB::raw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(account_name)), ' ', ''), '@', ''), '#', ''), '.', ''), '-', ''), '_', ''), '(', ''), ')', '') AS duplicate_name"))
                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(account_name)), ' ', ''), '@', ''), '#', ''), '.', ''), '-', ''), '_', ''), '(', ''), ')', '') IN ($placeholders)", $dn)->where('subgroup2', 19)->where('status', 1)
                    ->orderByRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(account_name)), ' ', ''), '@', ''), '#', ''), '.', ''), '-', ''), '_', ''), '(', ''), ')', '') ASC")
                    ->get();
            } else {
                $duplicate_customer = [];
            }


            $active_id = $id;
            $selectedSupp = [];

            $addSupplier = [];


            $action = false;
            $editData = [];

            if ($request->has('supplier_action')) {
                $poAction = $request->input('supplier_action');

                if ($poAction === 'add') {
                    $action = 'add';
                    $addSupplier = $this->getAddSupplier(); // Get data for adding supplier
                } elseif ($poAction === 'edit') {
                    $action = 'edit';
                    $editData = $this->getSupplierEdit($active_id); // Get all data for editing
                    // dd($editData);
                }elseif($poAction == 'createsupplier'){
                    $action = 'createsupplier';
                    $customer_id = $request->input('customer_id');
                    $editData = $this->getSupplierEdit($customer_id); // Get all data for editing
                }
            } else {
                if ($id) {
                    $selectedSupp = $this->getSupplierData($id);
                } else {
                    $firstRecord = $supplier->first();
                    if ($firstRecord) {
                        $active_id = $firstRecord->id;
                        $selectedSupp = $this->getSupplierData($firstRecord->id);
                    }
                }
            }

            return view('backEnd.cust-suppl.supplier_list', compact('supplier', 'staff', 'supplier_list', 'duplicate_customer', 'ctrl_vat', 'ctrl_company_name', 'ctrl_contact_name', 'ctrl_email', 'ctrl_sales_person', 'active_id', 'selectedSupp', 'editData', 'action', 'addSupplier'));
        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    // public function addSupplier()
    // {
    //     try {
    //         $r = SysHelper::get_data_by_role();
    //         $company_id = $r[0];

    //         $countries = SysCountries::all();
    //         $vattype = SysVatType::all();
    //         $vat = SysVat::select('sys_vat.*', 'sys_countries.name')->join('sys_countries', 'sys_countries.id', 'sys_vat.vat_country')->wherein('company_id', $company_id)->where('status', 1)->get();
    //         $accounts = SysChartofAccounts::where('status', 1)->get();
    //         $accounttype = SysAccountType::all();
    //         $roles = Role::where('active_status', '=', '1')->where('id', 2)->get();
    //         $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();

    //         $supplier_type = SysSupplierType::where('status', '=', '1')->get();
    //         $purchase_type = SysPurchaseType::where('status', '=', '1')->get();
    //         //$staffs = SmStaff::select('id','full_name')->where('active_status', '=', '1')->whereIn('designation_id', array(9,1,2,3))->get();
    //         $staffs = SysHelper::get_sales_persons2();
    //         $company = SysHelper::get_company_names();

    //         $designation = SmDesignation::select('id', 'title')->where('active_status', 1)->orderby('title', 'asc')->get();
    //         $department = SmHumanDepartment::select('id', 'name')->where('active_status', 1)->orderby('name', 'asc')->get();

    //         $address_cart = SysCustSupplAddressbookCart::select('sys_cust_suppl_addressbook_cart.*', 'sys_countries.name as c_name', 'sys_states.name as s_name')
    //             ->join('sys_countries', 'sys_countries.id', 'sys_cust_suppl_addressbook_cart.country')
    //             ->join('sys_states', 'sys_states.id', 'sys_cust_suppl_addressbook_cart.state')
    //             ->where('cart_id', session('logged_session_data.cart_id'))->get();

    //         $stl_bank = SysChartofAccounts::select('id', 'account_name')->where('status', 1)->wherein('company_id', $company_id)->where('stl', 1)->get();

    //         return view('backEnd.cust-suppl.addSupplier', compact('roles', 'paymentterms', 'staffs', 'accounts', 'accounttype', 'countries', 'vattype', 'supplier_type', 'purchase_type', 'vat', 'address_cart', 'designation', 'department', 'company', 'stl_bank'));

    //     } catch (\Throwable $th) {
    //         return $th;
    //     }

    // } 

    public function getAddSupplier()
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $countries = SysCountries::all();
            $vattype = SysVatType::all();
            $vat = SysVat::select('sys_vat.*', 'sys_countries.name')->join('sys_countries', 'sys_countries.id', 'sys_vat.vat_country')->wherein('company_id', $company_id)->where('status', 1)->get();
            $accounts = SysChartofAccounts::where('status', 1)->get();
            $accounttype = SysAccountType::all();
            $roles = Role::where('active_status', '=', '1')->where('id', 2)->get();
            $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();

            $supplier_type = SysSupplierType::where('status', '=', '1')->get();
            $purchase_type = SysPurchaseType::where('status', '=', '1')->get();
            //$staffs = SmStaff::select('id','full_name')->where('active_status', '=', '1')->whereIn('designation_id', array(9,1,2,3))->get();
            $staffs = SysHelper::get_sales_persons2();
            $company = SysHelper::get_company_names();

            $designation = SmDesignation::select('id', 'title')->where('active_status', 1)->orderby('title', 'asc')->get();
            $department = SmHumanDepartment::select('id', 'name')->where('active_status', 1)->orderby('name', 'asc')->get();

            $address_cart = SysCustSupplAddressbookCart::select('sys_cust_suppl_addressbook_cart.*', 'sys_countries.name as c_name', 'sys_states.name as s_name')
                ->join('sys_countries', 'sys_countries.id', 'sys_cust_suppl_addressbook_cart.country')
                ->join('sys_states', 'sys_states.id', 'sys_cust_suppl_addressbook_cart.state')
                ->where('cart_id', session('logged_session_data.cart_id'))->get();

            $stl_bank = SysChartofAccounts::select('id', 'account_name')->where('status', 1)->wherein('company_id', $company_id)->where('stl', 1)->get();

            return compact('roles', 'paymentterms', 'staffs', 'accounts', 'accounttype', 'countries', 'vattype', 'supplier_type', 'purchase_type', 'vat', 'address_cart', 'designation', 'department', 'company', 'stl_bank');

        } catch (\Throwable $th) {
            return $th;
        }

    }

    public function addSupplierStore(Request $request)
    {

        $input = $request->all();
        $dom = explode("@", $request->email);
        //$check = SysCustSuppl::select('id','code','name')->where('email', $request->email)->wherenotin('email', ['x','xx','xxx','xxxx'])->first();
        //$check = SysCustSuppl::select('id','code','name')->where('name', $request->customer_name)->where('catid',2)->first();
        //$check2 = SysCustSuppl::select('id','code','name')->where('name', $request->email)->where('catid',2)->first();

        if (SysHelper::check_supplier_is_added($request->customer_name) > 0) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

        // $supplier_documents = "";
        // if ($request->file('supplier_documents') != "") {
        //     $files = $request->file('supplier_documents');
        //     for ($i=0; $i<count($files); $i++) {
        //         $file1 = $files[$i];
        //         $supplier_documents = md5(time()) . "_customer_doc_".$i."." . $file1->getclientoriginalextension();
        //         $file1->move('public/uploads/cust-suppl/', $supplier_documents);
        //         $supplier_doc[]=$supplier_documents;
        //     }
        //     $supplier_documents = implode("|",$supplier_doc);
        // }

        //return $request->all();
        try {
            DB::beginTransaction();
            $company_access = "";
            if ($request->company_access != "") {
                $company_access = implode(",", $request->company_access);
            }
            if (!in_array(1, $request->company_access)) {
                $company_access = '1,' . $company_access;
            }
            $new_supplier = new SysCustSuppl();
            $new_supplier->group = SysHelper::get_supplier_group('group');
            $new_supplier->catid = 2;  // 1 customers, 2 suppliers
            $new_supplier->account_type = $request->account_type;
            $new_supplier->customer_salutation = $request->salutation;
            $new_supplier->first_name = $request->first_name;
            $new_supplier->designation = $request->designation;
            $new_supplier->last_name = $request->last_name;
            $new_supplier->name = $request->customer_name;
            $new_supplier->customer_name_display = $request->customer_name_display;
            $new_supplier->code = SysHelper::get_new_supplier_code();
            // $new_supplier->address = $request->address;
            // $new_supplier->address2 = $request->address2;


            $new_supplier->city = $request->city;
            $new_supplier->area = $request->billing_area;
            $new_supplier->building_name = $request->billing_building_name;
            $new_supplier->flat_office_no = $request->billing_flat_office_shop_no;


            $new_supplier->contcat_person = $request->e_first_name[0];
            $new_supplier->contcat_number = $request->mobile_code;
            $new_supplier->mobile = $request->mobile;
            $new_supplier->email = $request->email;
            $new_supplier->sales_person = Auth::user()->id;
            //$new_customer->vat_type = $request->vat_type;
            $new_supplier->supplier_type = $request->supplier_type;
            $new_supplier->purchase_type = $request->purchase_type;
            $new_supplier->vat_country = $request->country_vat;
            //$new_supplier->vat_state = $request->state_vat;
            $new_supplier->vat_state = $request->vat_state;
            $new_supplier->vat_percentage = $request->vat_percentage;
            $new_supplier->vat_number = $request->vat_number;

            $new_supplier->country_telephone = $request->country_telephone ?: null;
            $new_supplier->internal = $request->internal;


            

            $new_supplier->credit_limit = str_replace(',', '', $request->credit_limit) ?: null;
            $new_supplier->credit_days = $request->credit_days ?: null;
            $new_supplier->payment_terms = $request->payment_terms;
            $new_supplier->payment_terms_txt = $request->payment_terms_txt;
            $new_supplier->transaction_type = $request->transaction_type;
            $new_supplier->company_access = $company_access;
            //$new_supplier->customer_documents = $supplier_documents;
            $new_supplier->status = 1;
            if ($request->vat_percentage_fixed) {
                $new_supplier->vat_is_fixed = 1;
            }
            $new_supplier->created_by = Auth::user()->id;
            $new_supplier->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $new_supplier->type = $request->type;
            $new_supplier->company_id = session('logged_session_data.company_id');

            $new_supplier->vendor_name = $request->vendor_name;
            $new_supplier->beneficiary_name = $request->beneficiary_name;
            $new_supplier->iban = $request->iban;
            $new_supplier->swift_code = $request->swift_code;
            $new_supplier->city_country = $request->city_country;

            $new_supplier->website = $request->customer_website;
            $new_supplier->maps_location = $request->maps_location;

            $new_supplier->stl = $request->stl;
            // if($request->stl==0){
            //     $new_supplier->stl_bank = 0;
            //     $new_supplier->stl_limit = 0;
            //     $new_supplier->stl_per_trn_limit = 0;
            //     $new_supplier->stl_opb = 0;
            // } else {
            //     $new_supplier->stl_bank = $request->stl_bank;
            //     $new_supplier->stl_limit = floatval(str_replace(',', '', $request->stl_limit));
            //     $new_supplier->stl_per_trn_limit = floatval(str_replace(',', '', $request->stl_per_trn_limit));
            //     $new_supplier->stl_opb = floatval(str_replace(',', '', $request->stl_opb));
            // }

            $results1 = $new_supplier->save();

             if ($request->filled('customer_id')) {
                $new_supplier->customer_id = $request->customer_id;
                $new_supplier->save();
                $customer = SysCustSuppl::find($request->customer_id);
                if ($customer) {
                    $customer->supplier_id = $new_supplier->id;
                    $customer->save();
                }

                // move supplier existing_doc_id to customer_documents

                if ($request->existing_doc_id) {

                


                    foreach ($request->existing_doc_id as $key => $value) {

                        $oldDoc = SysCustSupplDoc::find($value);


                        // Save NEW record
                        DB::table('sys_cust_suppl_doc')->insert([
                            'cust_suppl_id' => $new_supplier->id,
                            'doc_name' => $oldDoc->doc_name,
                            'doc_file' => $oldDoc->doc_file,   // IMPORTANT: new file name
                            'doc_exp_date' => $oldDoc->doc_exp_date,
                            'status' => 1,
                            'created_by' => Auth::id(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }



            }

            if ($request->stl == 1 && count($request->stl_bank) > 0) {
                for ($i = 0; $i < count($request->stl_bank); $i++) {
                    $stl = new SysCustSupplSTL();
                    $stl->cust_suppl_id = $new_supplier->id;
                    $stl->stl_bank = $request->stl_bank[$i];
                    $stl->stl_dept = $request->stl_dept[$request->stl_bank[$i]];
                    $stl->stl_limit = $request->stl_limit[$request->stl_bank[$i]];
                    $stl->stl_per_trn_limit = $request->stl_per_trn_limit[$request->stl_bank[$i]];
                    $stl->stl_opb = $request->stl_opb[$request->stl_bank[$i]];
                    $stl->company_id = session('logged_session_data.company_id');
                    $stl->status = 1;
                    $stl->created_by = Auth::user()->id;
                    $stl->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                    $stl->save();
                }
            }

            // for($i = 0; $i < count($request->sales_person); $i++) {
            //     DB::table('sys_cust_suppl_assign')->insert(
            //         [
            //             'cust_supp_id' => $new_supplier->id,
            //             'user_id' => $request->sales_person[$i],
            //             'type' => 1, //1 customers, 2 suppliers
            //         ]);
            // }

            DB::table('sys_cust_suppl_addressbook')->where('cust_suppl_id', $new_supplier->id)->update(['set_default' => 0]);
            DB::table('sys_cust_suppl_contact')->where('cust_suppl_id', $new_supplier->id)->update(['set_default' => 0]);

            $address = new SysCustSupplAddressbook();
            $address->cust_suppl_id = $new_supplier->id;
            // $address->address = $request->address;
            // $address->address2 = $request->address2;
            $address->area = $request->billing_area;
            $address->building_name = $request->billing_building_name;
            $address->flat_office_no = $request->billing_flat_office_shop_no;
            $address->city = $request->city;
            $address->country = $request->country;
            $address->state = $request->state;
            $address->zip_code = $request->zip_code;
            $address->set_default = 1;
            $address->company_id = session('logged_session_data.company_id');
            $address->is_shipping = 0;
            $address->status = 1;
            $address->created_by = Auth::user()->id;
            $results = $address->save();

            if ($request->same_billing_address) {
                $address = new SysCustSupplAddressbook();
                $address->cust_suppl_id = $new_supplier->id;
                $address->area = $request->billing_area;
                $address->building_name = $request->billing_building_name;
                $address->flat_office_no = $request->billing_flat_office_shop_no;
                $address->city = $request->city;
                $address->country = $request->country;
                $address->state = $request->state;
                $address->zip_code = $request->zip_code;
                $address->set_default = 1;
                $address->company_id = session('logged_session_data.company_id');
                $address->is_shipping = 1;
                $address->status = 1;
                $address->created_by = Auth::user()->id;
                $results = $address->save();
            } else {
                $address = new SysCustSupplAddressbook();
                $address->cust_suppl_id = $new_supplier->id;
                // $address->address = $request->address_ship;
                // $address->address2 = $request->address2_ship;
                $address->area = $request->shipping_area;
                $address->building_name = $request->shipping_building_name;
                $address->flat_office_no = $request->shipping_flat_office_shop_no;

                $address->city = $request->city_ship;
                $address->country = $request->country_ship;
                $address->state = $request->state_ship;
                $address->zip_code = $request->zip_code_ship;
                $address->set_default = 1;
                $address->company_id = session('logged_session_data.company_id');
                $address->is_shipping = 1;
                $address->status = 1;
                $address->created_by = Auth::user()->id;
                $results = $address->save();
            }

            $cart_address = SysCustSupplAddressbookCart::where('cart_id', session('logged_session_data.cart_id'))->get();
            if (count($cart_address) > 0) {
                foreach ($cart_address as $key) {
                    $address = new SysCustSupplAddressbook();
                    $address->cust_suppl_id = $new_supplier->id;
                    // $address->address = $key->address;
                    // $address->address2 = $key->address2;
                    $address->area = $key->area;
                    $address->building_name = $key->building_name;
                    $address->flat_office_no = $key->flat_office_no;
                    $address->city = $key->city;
                    $address->country = $key->country;
                    $address->state = $key->state;
                    $address->zip_code = $key->zip_code;
                    $address->set_default = $key->set_default;
                    $address->company_id = session('logged_session_data.company_id');
                    $address->is_shipping = $key->is_shipping;
                    $address->status = 1;
                    $address->created_by = Auth::user()->id;
                    $results = $address->save();
                }
            }
            SysCustSupplAddressbookCart::where('cart_id', session('logged_session_data.cart_id'))->delete();

            for ($i = 0; $i < count($request->e_first_name); $i++) {
                if ($request->e_first_name[$i] != "" && $request->e_email_address[$i] != "" && ($request->e_mobile[$i] != "")) {
                    $contact = new SysCustSupplContact();
                    $contact->cust_suppl_id = $new_supplier->id;
                    $contact->salutation = $request->e_salutation[$i];
                    $contact->first_name = $request->e_first_name[$i];
                    $contact->last_name = $request->e_last_name[$i];
                    $contact->email_address = $request->e_email_address[$i];
                    // $contact->work_phone = $request->e_work_phone[$i];
                    $contact->mobile = $request->e_mobile[$i];
                    $contact->designation = $request->e_designation[$i];
                    $contact->department = $request->e_department[$i];
                    $contact->status = 1;
                    $contact->set_default = 1;
                    $contact->company_id = session('logged_session_data.company_id');
                    $contact->created_by = Auth::user()->id;
                    $results = $contact->save();
                }
            }

            for ($i = 1; $i <= count($request->doc_name); $i++) {
                if ($request->file('customer_documents_' . $i) != "") {
                    $doc_exp_date = date('Y-m-d');
                    if ($i == 1) {
                        $doc_exp_date = SysHelper::normalizeToYmd($request->doc_exp_date[$i - 1]);
                        DB::table('sys_cust_suppl')->where('id', $new_supplier->id)->update(['is_file' => 1]);
                    }
                    if ($i == 2) {
                        DB::table('sys_cust_suppl')->where('id', $new_supplier->id)->update(['is_file' => 2]);
                    }
                    $file = $request->file('customer_documents_' . $i);
                    $company_doc = md5($file->getClientOriginalName() . time()) . "_customer_doc_" . $i . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/cust-suppl/', $company_doc);
                    DB::table('sys_cust_suppl_doc')->insert([
                        'cust_suppl_id' => $new_supplier->id,
                        'doc_name' => $request->doc_name[$i - 1],
                        'doc_file' => $company_doc,
                        'doc_exp_date' => $doc_exp_date,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                    ]);
                }
            }

            $accounts = new SysChartofAccounts();
            $accounts->account_code = $new_supplier->code;
            $accounts->account_name = $request->customer_name;
            $accounts->group = SysHelper::get_supplier_group('group');
            $accounts->subgroup = SysHelper::get_supplier_group('subgroup');
            $accounts->subgroup2 = SysHelper::get_supplier_group('subgroup2');
            $accounts->company_id = session('logged_session_data.company_id');
            $accounts->company_access = $company_access;
            $accounts->status = 1;
            $accounts->stl = $request->stl;
            $accounts->created_by = Auth::user()->id;
            $accounts->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $results = $accounts->save();

            session()->forget('subgroup2');
            DB::commit();

            if ($request->btnSubmit == 'createcustomer') {
                Toastr::success('Supplier Created Successfully ! Please Create Customer', 'Success');
                 return redirect('customers?customer_action=createcustomer&supplier_id=' . $new_supplier->id);
            }




            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($results) {
                    return ApiBaseMethod::sendResponse(null, 'New Supplier has been added successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } else {
                if ($results) {

                    Toastr::success('Operation successful', 'Success');
                    return redirect('suppliers/' . $new_supplier->id);
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }

    public function getSupplierEdit($id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $countries = SysCountries::all();
            $states = SysStates::all();
            $vattype = SysVatType::all();
            $vat = SysVat::select('sys_vat.*', 'sys_countries.name')->join('sys_countries', 'sys_countries.id', 'sys_vat.vat_country')->wherein('company_id', $company_id)->where('status', 1)->get();
            $accounts = SysChartofAccounts::where('status', 1)->wherein('company_id', $company_id)->get();
            $accounttype = SysAccountType::all();
            $roles = Role::where('active_status', '=', '1')->where('id', 2)->get();
            $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();

            $supplier_type = SysSupplierType::where('status', '=', '1')->get();
            $purchase_type = SysPurchaseType::all();
            $staffs = SysHelper::get_sales_persons2();
            $company = SysHelper::get_company_names();

            $designation = SmDesignation::select('id', 'title')->where('active_status', 1)->orderby('title', 'asc')->get();
            $department = SmHumanDepartment::select('id', 'name')->where('active_status', 1)->orderby('name', 'asc')->get();

            $editData = SysCustSuppl::where('id', $id)->first();
            $editAddressbook = SysCustSupplAddressbook::where('cust_suppl_id', $id)->get();
            $editContact = SysCustSupplContact::where('cust_suppl_id', $id)->get();
            $editDoc = SysCustSupplDoc::where('cust_suppl_id', $id)->get();
            $editAssign = DB::table('sys_cust_suppl_assign')->where('cust_supp_id', $id)->get();

            $stl_bank = SysChartofAccounts::select('id', 'account_name')->where('status', 1)->wherein('company_id', $company_id)->where('stl', 1)->get();
            $stl_det = SysCustSupplSTL::where('status', 1)->where('cust_suppl_id', $id)->get();

            return compact('roles', 'paymentterms', 'staffs', 'accounts', 'accounttype', 'countries', 'vattype', 'supplier_type', 'purchase_type', 'vat', 'editData', 'editAddressbook', 'editContact', 'editDoc', 'editAssign', 'states', 'designation', 'department', 'company', 'stl_bank', 'stl_det');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    // public function supplierEdit($id)
    // {
    //     try {
    //         $r = SysHelper::get_data_by_role();
    //         $company_id = $r[0];
    //         $countries = SysCountries::all();
    //         $states = SysStates::all();
    //         $vattype = SysVatType::all();
    //         $vat = SysVat::select('sys_vat.*', 'sys_countries.name')->join('sys_countries', 'sys_countries.id', 'sys_vat.vat_country')->wherein('company_id', $company_id)->where('status', 1)->get();
    //         $accounts = SysChartofAccounts::where('status', 1)->wherein('company_id', $company_id)->get();
    //         $accounttype = SysAccountType::all();
    //         $roles = Role::where('active_status', '=', '1')->where('id', 2)->get();
    //         $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();

    //         $supplier_type = SysSupplierType::where('status', '=', '1')->get();
    //         $purchase_type = SysPurchaseType::all();
    //         $staffs = SysHelper::get_sales_persons2();
    //         $company = SysHelper::get_company_names();

    //         $designation = SmDesignation::select('id', 'title')->where('active_status', 1)->orderby('title', 'asc')->get();
    //         $department = SmHumanDepartment::select('id', 'name')->where('active_status', 1)->orderby('name', 'asc')->get();

    //         $editData = SysCustSuppl::where('id', $id)->first();
    //         $editAddressbook = SysCustSupplAddressbook::where('cust_suppl_id', $id)->get();
    //         $editContact = SysCustSupplContact::where('cust_suppl_id', $id)->get();
    //         $editDoc = SysCustSupplDoc::where('cust_suppl_id', $id)->get();
    //         $editAssign = DB::table('sys_cust_suppl_assign')->where('cust_supp_id', $id)->get();

    //         $stl_bank = SysChartofAccounts::select('id', 'account_name')->where('status', 1)->wherein('company_id', $company_id)->where('stl', 1)->get();
    //         $stl_det = SysCustSupplSTL::where('status', 1)->where('cust_suppl_id', $id)->get();

    //         return view('backEnd.cust-suppl.editSupplier', compact('roles', 'paymentterms', 'staffs', 'accounts', 'accounttype', 'countries', 'vattype', 'supplier_type', 'purchase_type', 'vat', 'editData', 'editAddressbook', 'editContact', 'editDoc', 'editAssign', 'states', 'designation', 'department', 'company', 'stl_bank', 'stl_det'));
    //     } catch (\Exception $e) {
    //         Toastr::error('Operation Failed', 'Failed');
    //         return redirect()->back();
    //     }
    // }

    public function supplierInactive(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $account_code = SysCustSuppl::select('code')->where('id', $id)->first();
            SysCustSuppl::where('id', $id)->update(['status' => 2, 'delete_reason' => $request->delete_reason]);
            SysChartofAccounts::where('account_code', $account_code->code)->update(['status' => 2]);
            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();

        } catch (\Throwable $th) {
            DB::rollBack();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function supplierRestore(Request $request, $id)
    {
        try {

            DB::beginTransaction();
            $account_code = SysCustSuppl::select('code')->where('id', $id)->first();
            SysCustSuppl::where('id', $id)->update(['status' => 1, 'delete_reason' => $request->restore_reason]);
            SysChartofAccounts::where('account_code', $account_code->code)->update(['status' => 1]);
            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();

        } catch (\Throwable $th) {
            DB::rollBack();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function supplierUpdate(Request $request)
    {

        $input = $request->all();
        //return $input;
        $dom = explode("@", $request->email);
        //$check = SysCustSuppl::select('id','code','name')->where('email', $request->email)->wherenotin('email', ['x','xx','xxx','xxxx'])->first();
        //$check = SysCustSuppl::select('id','code','name')->where('name', $request->customer_name)->first();
        //if(isset($check)){
        //Toastr::error('Operation Failed', 'Failed');
        //return redirect()->back(); 
        //}
        try {
            DB::beginTransaction();
            $company_access = "";
            if ($request->company_access != "") {
                $company_access = implode(",", $request->company_access);
            }
            if (!in_array(1, $request->company_access)) {
                $company_access = '1,' . $company_access;
            }
            $new_customer = SysCustSuppl::find($request->cust_id);
            $new_customer->customer_salutation = $request->salutation;
            $new_customer->first_name = $request->first_name;
            $new_customer->designation = $request->designation;
            $new_customer->last_name = $request->last_name;
            if (SysHelper::check_supplier_is_added($request->customer_name) == 0) {
                $new_customer->name = $request->customer_name;
                $new_customer->customer_name_display = $request->customer_name_display;
                SysChartofAccounts::where('account_code', $new_customer->code)->update(['account_name' => $request->customer_name, 'company_access' => $company_access, 'stl' => $request->stl]);
            }

            //$new_customer->name = $request->customer_name;
            //$new_customer->customer_name_display = $request->customer_name_display;
            $new_customer->contcat_person = $request->e_first_name[0];
            $new_customer->contcat_number = $request->mobile_code;
            $new_customer->mobile = $request->mobile;
            $new_customer->email = $request->email;
            $new_customer->sales_person = Auth::user()->id;
            //$new_customer->vat_type = $request->vat_type;
            $new_customer->supplier_type = $request->supplier_type;
            $new_customer->purchase_type = $request->purchase_type;
            $new_customer->vat_country = $request->country_vat;
            //$new_customer->vat_state = $request->state_vat;
            $new_customer->vat_state = $request->vat_state;
            $new_customer->account_type = $request->account_type;
            $new_customer->internal = $request->internal;
            // $new_customer->type = $request->type;

            $new_customer->city = $request->city;
            $new_customer->zip_code = $request->zip_code;
            $new_customer->vat_percentage = $request->vat_percentage;
            $new_customer->vat_number = $request->vat_number;
            $new_customer->credit_limit = str_replace(',', '', $request->credit_limit) ?: null;
            $new_customer->credit_days = $request->credit_days ?: null;
            $new_customer->payment_terms = $request->payment_terms;
            $new_customer->payment_terms_txt = $request->payment_terms_txt;
            $new_customer->transaction_type = $request->transaction_type;

            $new_customer->country_telephone = $request->country_telephone ?: null;


            //$new_customer->customer_documents = $customer_documents;
            $new_customer->status = 1;
            if ($request->vat_percentage_fixed) {
                $new_customer->vat_is_fixed = 1;
            }
            $new_customer->updated_by = Auth::user()->id;
            $new_customer->updated_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $new_customer->type = $request->type;
            $new_customer->company_access = $company_access;
            // $new_customer->company_id = session('logged_session_data.company_id');

            $new_customer->vendor_name = $request->vendor_name;
            $new_customer->beneficiary_name = $request->beneficiary_name;
            $new_customer->iban = $request->iban;
            $new_customer->swift_code = $request->swift_code;
            $new_customer->city_country = $request->city_country;

            $new_customer->stl = $request->stl;
            // if($request->stl==0){
            //     $new_customer->stl_bank = 0;
            //     $new_customer->stl_limit = 0;
            //     $new_customer->stl_per_trn_limit = 0;
            //     $new_customer->stl_opb = 0;
            // } else {
            //     $new_customer->stl_bank = $request->stl_bank;
            //     $new_customer->stl_limit = floatval(str_replace(',', '', $request->stl_limit));
            //     $new_customer->stl_per_trn_limit = floatval(str_replace(',', '', $request->stl_per_trn_limit));
            //     $new_customer->stl_opb = floatval(str_replace(',', '', $request->stl_opb));
            // }

            $new_customer->website = $request->customer_website;
            $new_customer->maps_location = $request->maps_location;

            $results = $new_customer->save();


            SysCustSupplSTL::where('cust_suppl_id', $new_customer->id)->delete();
            if ($request->stl == 1 && count($request->stl_bank) > 0) {
                for ($i = 0; $i < count($request->stl_bank); $i++) {
                    $stl = new SysCustSupplSTL();
                    $stl->cust_suppl_id = $new_customer->id;
                    $stl->stl_bank = $request->stl_bank[$i];
                    $stl->stl_dept = $request->stl_dept[$request->stl_bank[$i]];
                    $stl->stl_limit = floatval(str_replace(',', '', $request->stl_limit[$request->stl_bank[$i]]));
                    $stl->stl_per_trn_limit = floatval(str_replace(',', '', $request->stl_per_trn_limit[$request->stl_bank[$i]]));
                    $stl->stl_opb = floatval(str_replace(',', '', $request->stl_opb[$request->stl_bank[$i]]));
                    $stl->company_id = session('logged_session_data.company_id');
                    $stl->status = 1;
                    $stl->created_by = Auth::user()->id;
                    $stl->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                    $stl->save();
                }
            }

            DB::table('sys_cust_suppl_addressbook')->where('cust_suppl_id', $request->cust_id)->update(['set_default' => 0]);
            DB::table('sys_cust_suppl_contact')->where('cust_suppl_id', $request->cust_id)->update(['set_default' => 0]);

            SysCustSupplContact::where('cust_suppl_id', $request->cust_id)->delete();
            for ($i = 0; $i < count($request->e_first_name); $i++) {
                if ($request->e_first_name[$i] != "" && $request->e_email_address[$i] != "" && ($request->e_work_phone[$i] != "" || $request->e_mobile[$i] != "")) {
                    $contact = new SysCustSupplContact();
                    $contact->cust_suppl_id = $request->cust_id;
                    $contact->salutation = $request->e_salutation[$i];
                    $contact->first_name = $request->e_first_name[$i];
                    $contact->last_name = $request->e_last_name[$i];
                    $contact->email_address = $request->e_email_address[$i];
                    $contact->work_phone = $request->e_work_phone[$i];
                    $contact->mobile = $request->e_mobile[$i];
                    $contact->designation = $request->e_designation[$i];
                    $contact->department = $request->e_department[$i];
                    $contact->status = 1;
                    $contact->set_default = 1;
                    $contact->company_id = session('logged_session_data.company_id');
                    $contact->created_by = Auth::user()->id;
                    $results = $contact->save();
                }
            }

            for ($i = 1; $i <= count($request->doc_name); $i++) {
                if ($request->file('customer_documents_' . $i) != "") {
                    $doc_exp_date = date('Y-m-d');
                    if ($i == 1) {
                        $doc_exp_date = SysHelper::normalizeToYmd($request->doc_exp_date[$i - 1]);
                        DB::table('sys_cust_suppl')->where('id', $new_customer->id)->update(['is_file' => 1]);
                    }
                    if ($i == 2) {
                        DB::table('sys_cust_suppl')->where('id', $new_customer->id)->update(['is_file' => 2]);
                    }
                    $file = $request->file('customer_documents_' . $i);
                    $company_doc = md5($file->getClientOriginalName() . time()) . "_customer_doc_" . $i . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/cust-suppl/', $company_doc);
                    DB::table('sys_cust_suppl_doc')->insert([
                        'cust_suppl_id' => $new_customer->id,
                        'doc_name' => $request->doc_name[$i - 1],
                        'doc_file' => $company_doc,
                        'doc_exp_date' => $doc_exp_date,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                    ]);
                }
            }
            //SysChartofAccounts::where('account_code',$new_customer->code)->update(['account_name' => $new_customer->name,'company_access' => $company_access]);
            //SysChartofAccounts::where('account_code',$new_customer->code)->update(['company_access' => $company_access]);

            session()->forget('subgroup2');




            $address = SysCustSupplAddressbook::find($request->billing_address_id);
            if (!$address) {
                $address = new SysCustSupplAddressbook();
            }
            $address->cust_suppl_id = $new_customer->id;
            // $address->address = $request->address;
            // $address->address2 = $request->address2;

            $address->area = $request->billing_area;
            $address->building_name = $request->billing_building_name;
            $address->flat_office_no = $request->billing_flat_office_shop_no;

            $address->city = $request->city;
            $address->country = $request->country;
            if ($request->state == "") {
                $address->state = 0;
            } else {
                $address->state = $request->state;
            }
            $address->zip_code = $request->zip_code;
            $address->set_default = 1;
            $address->company_id = session('logged_session_data.company_id');
            $address->is_shipping = 0;
            $address->status = 1;
            $address->created_by = Auth::user()->id;

            $results = $address->save();


            $address_s = SysCustSupplAddressbook::find($request->shipping_address_id);
            if (!$address_s) {
                $address_s = new SysCustSupplAddressbook();
            }
            $address_s->cust_suppl_id = $new_customer->id;
            // $address->address = $request->address_ship;
            // $address->address2 = $request->address2_ship;
            $address_s->area = $request->shipping_area;
            $address_s->building_name = $request->shipping_building_name;
            $address_s->flat_office_no = $request->shipping_flat_office_shop_no;
            $address_s->city = $request->city_ship;
            $address_s->country = $request->country_ship;
            if ($request->state_ship == "") {
                $address_s->state = 0;
            } else {
                $address_s->state = $request->state_ship;
            }
            $address_s->zip_code = $request->zip_code_ship;
            $address_s->set_default = 1;
            $address_s->company_id = session('logged_session_data.company_id');
            $address_s->is_shipping = 1;
            $address_s->status = 1;
            $address_s->created_by = Auth::user()->id;
            $results = $address_s->save();

            DB::commit();


            if ($results) {
                Toastr::success('Operation successful', 'Success');
                return redirect('suppliers/' . $new_customer->id);
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    //end customer update 


    function add_supplier_script(Request $request)
    {
        try {
            $address = new SysCustSupplAddressbookCart();
            $address->cart_id = session('logged_session_data.cart_id');
            $address->cust_suppl_id = 0;
            $address->area = $request->area;
            $address->building_name = $request->building_name;
            $address->flat_office_no = $request->flat_office_shop_no;
            $address->city = $request->city;
            $address->country = $request->country;
            $address->state = $request->state;
            $address->zip_code = $request->zip_code;
            $address->set_default = $request->set_default;
            $address->company_id = session('logged_session_data.company_id');
            $address->is_shipping = $request->address_type;
            $address->status = 1;
            $address->created_by = Auth::user()->id;
            $results = $address->save();

            $ret = SysCustSupplAddressbookCart::select('sys_cust_suppl_addressbook_cart.*', 'sys_countries.name as c_name', 'sys_states.name as s_name')
                ->join('sys_countries', 'sys_countries.id', 'sys_cust_suppl_addressbook_cart.country')
                ->join('sys_states', 'sys_states.id', 'sys_cust_suppl_addressbook_cart.state')
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

    function delete_supplier_script(Request $request)
    {
        try {

            SysCustSupplAddressbookCart::where('id', $request->id)->delete();

            $ret = SysCustSupplAddressbookCart::select('sys_cust_suppl_addressbook_cart.*', 'sys_countries.name as c_name', 'sys_states.name as s_name')
                ->join('sys_countries', 'sys_countries.id', 'sys_cust_suppl_addressbook_cart.country')
                ->join('sys_states', 'sys_states.id', 'sys_cust_suppl_addressbook_cart.state')
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



    public function deleteSupplier($id)
    {
        // try{
        //     $staffs = SmStaff::find($id);
        //     $staffs->active_status = 0;
        //     $result = $staffs->update();
        //     if ($result) {
        //         $users = User::find($staffs->user_id);
        //         $users->active_status = 0;
        //         $results = $users->update();
        //     }
        //     Toastr::success('Operation successful', 'Success');
        //     return redirect()->back();
        // }catch (\Exception $e) {
        //    Toastr::error('Operation Failed', 'Failed');
        //    return redirect()->back(); 
        // }
    }

    public function loginAccessPermission(Request $request)
    {

        // if($request->status == 'on'){
        //     $status = 1;
        // }else{
        //     $status = 0;
        // }

        // $staff = SmStaff::find($request->id);
        // $user_id = $staff->user_id;
        // $staff->active_status = $status;
        // $staff->save();
        // $user = User::find($user_id);
        // $user->access_status = $status;
        // $user->save();
        // return response()->json($request->id);
    }

    // import

    public function supplier_import(Request $request)
    {
        try {
            $data = DB::table('sys_suppl_import as i')->select('i.*')->where('i.status', 1)->where('i.company_id', session('logged_session_data.company_id'))->get();
            $customer = DB::table('sys_cust_suppl')->select('name')->where('company_id', session('logged_session_data.company_id'))->where('catid', 2)->get(); // cat - 1 customers, 2 suppliers
            $sales_person = DB::table('sm_staffs')->select('user_id', 'first_name')->where('company_id', session('logged_session_data.company_id'))->get();
            $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();
            $supplier_type = SysSupplierType::where('status', '=', '1')->get();
            $purchase_type = SysPurchaseType::where('status', '=', '1')->get();
            $country = SysCountries::get();
            $state = SysStates::get();

            return view('backEnd.cust-suppl.importsupplier', compact('data', 'customer', 'sales_person', 'paymentterms', 'supplier_type', 'purchase_type', 'country', 'state'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function supplier_import_list(Request $request)
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
                    $dataArray[0][8] => $dataArray[$i][8],
                    $dataArray[0][9] => $dataArray[$i][9],
                    $dataArray[0][10] => $dataArray[$i][10],
                    $dataArray[0][11] => $dataArray[$i][11],
                    $dataArray[0][12] => $dataArray[$i][12],
                    $dataArray[0][13] => $dataArray[$i][13],
                    $dataArray[0][14] => $dataArray[$i][14],
                    $dataArray[0][15] => $dataArray[$i][15],
                    $dataArray[0][16] => $dataArray[$i][16],
                    $dataArray[0][17] => $dataArray[$i][17],
                    $dataArray[0][18] => $dataArray[$i][18],
                    $dataArray[0][19] => $dataArray[$i][19],
                    $dataArray[0][20] => $dataArray[$i][20],
                    $dataArray[0][21] => $dataArray[$i][21],
                    $dataArray[0][22] => $dataArray[$i][22],
                    $dataArray[0][23] => $dataArray[$i][23],
                    $dataArray[0][24] => $dataArray[$i][24],
                    $dataArray[0][25] => $dataArray[$i][25],
                    $dataArray[0][26] => $dataArray[$i][26],
                    $dataArray[0][27] => $dataArray[$i][27],
                    $dataArray[0][28] => $dataArray[$i][28],
                    'created_by' => Auth::user()->id,
                    'company_id' => session('logged_session_data.company_id'),
                ];
                //}
                //$data2[]=$data;

            }

            foreach (array_chunk($data, 1000) as $dt) {
                SysSupplImport::insert($dt);
            }

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

    public function supplier_import_clear(Request $request)
    {
        try {
            SysSupplImport::where('company_id', session('logged_session_data.company_id'))->delete();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function supplier_import_data(Request $request)
    {
        try {
            DB::beginTransaction();
            $customer = DB::table('sys_cust_suppl')->where('company_id', session('logged_session_data.company_id'))->where('catid', 1)->pluck('name');

            $sales_person = DB::table('sm_staffs')->select('user_id', 'first_name')->where('company_id', session('logged_session_data.company_id'))->get();
            $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();
            $supplier_type = SysSupplierType::where('status', '=', '1')->get();
            $purchase_type = SysPurchaseType::where('status', '=', '1')->get();
            $country = SysCountries::get();
            $state = SysStates::get();

            $data = DB::table('sys_suppl_import as i')->select('i.*')->where('i.status', 1)->where('i.company_id', session('logged_session_data.company_id'))
                ->whereNotIn('i.name', $customer)->get();

            $group = SysHelper::get_supplier_group('group');
            $subgroup = SysHelper::get_supplier_group('subgroup');
            $subgroup2 = SysHelper::get_supplier_group('subgroup2');

            if (count($data) > 0) {
                foreach ($data as $dt) {

                    $sales_person_id = $sales_person->where('first_name', $dt->sales_person)->max('user_id');
                    $supplier_type_id = $supplier_type->where('title', $dt->supplier_type)->max('id');
                    $purchase_type_id = $purchase_type->where('title', $dt->purchase_type)->max('id');
                    $payment_terms_id = $paymentterms->where('title', $dt->payment_terms)->max('id');
                    $country_id = $country->where('name', $dt->country)->max('id');
                    $state_id = $state->where('name', $dt->state)->max('id');
                    $vat_country = $country->where('name', $dt->vat_country)->max('id');


                    $new_supplier = new SysCustSuppl();
                    $new_supplier->group = $group;
                    $new_supplier->catid = 2;  // 1 customers, 2 suppliers
                    $new_supplier->customer_salutation = $dt->customer_salutation;
                    $new_supplier->first_name = $dt->first_name;
                    $new_supplier->designation = $dt->designation;
                    $new_supplier->last_name = $dt->last_name;
                    $new_supplier->name = $dt->name;
                    $new_supplier->customer_name_display = $dt->customer_name_display;
                    $new_supplier->code = SysHelper::get_new_supplier_code();
                    $new_supplier->address = $dt->address;
                    $new_supplier->address2 = $dt->address2;
                    $new_supplier->contcat_person = $dt->contcat_person_first_name . ' ' . $dt->contcat_person_last_name;
                    $new_supplier->contcat_number = $dt->contcat_number;
                    $new_supplier->mobile = $dt->mobile;
                    $new_supplier->email = $dt->email;
                    $new_supplier->sales_person = $sales_person_id;
                    $new_supplier->supplier_type = $supplier_type_id;
                    $new_supplier->purchase_type = $purchase_type_id;
                    $new_supplier->vat_country = $vat_country;
                    $new_supplier->city = $dt->city;
                    $new_supplier->zip_code = $dt->zip_code;
                    $new_supplier->vat_percentage = $dt->vat_percentage;
                    $new_supplier->vat_number = $dt->vat_number;
                    $new_supplier->credit_limit = str_replace(',', '', $dt->credit_limit);
                    $new_supplier->credit_days = $dt->credit_days;
                    $new_supplier->payment_terms = $payment_terms_id;
                    $new_supplier->transaction_type = $dt->transaction_type;
                    //$new_customer->customer_documents = $customer_documents;
                    $new_supplier->status = 1;
                    $new_supplier->vat_is_fixed = $dt->vat_is_fixed;
                    $new_supplier->created_by = Auth::user()->id;
                    //$new_customer->created_at = '';
                    $new_supplier->type = 1;
                    $new_supplier->company_id = $dt->company_id;
                    $results1 = $new_supplier->save();

                    $address = new SysCustSupplAddressbook();
                    $address->cust_suppl_id = $new_supplier->id;
                    $address->address = $dt->address;
                    $address->address2 = $dt->address2;
                    $address->city = $dt->city;
                    $address->country = $country_id;
                    $address->state = $state_id;
                    $address->zip_code = $dt->zip_code;
                    $address->set_default = 1;
                    $address->company_id = $dt->company_id;
                    $address->is_shipping = 1;
                    $address->status = 1;
                    $address->created_by = Auth::user()->id;
                    $results = $address->save();

                    $contact = new SysCustSupplContact();
                    $contact->cust_suppl_id = $new_supplier->id;
                    $contact->salutation = $dt->contcat_person_salutation;
                    $contact->first_name = $dt->contcat_person_first_name;
                    $contact->last_name = $dt->contcat_person_last_name;
                    $contact->email_address = $dt->email;
                    $contact->work_phone = $dt->contcat_number;
                    $contact->mobile = $dt->mobile;
                    $contact->designation = $dt->designation;
                    //$contact->department = $dt->e_department[$i];
                    $contact->status = 1;
                    $contact->set_default = 1;
                    $contact->company_id = $dt->company_id;
                    $contact->created_by = Auth::user()->id;
                    $results = $contact->save();

                    $accounts = new SysChartofAccounts();
                    $accounts->account_code = $new_supplier->code;
                    $accounts->account_name = $new_supplier->name;
                    $accounts->group = $group;
                    $accounts->subgroup = $subgroup;
                    $accounts->subgroup2 = $subgroup2;
                    $accounts->status = 1;
                    $accounts->company_id = $dt->company_id;
                    $accounts->company_access = $dt->company_id;
                    $accounts->created_by = Auth::user()->id;
                    $results = $accounts->save();

                }

                SysSupplImport::where('company_id', session('logged_session_data.company_id'))->delete();
                DB::commit();
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
            Toastr::success('Supplier Imported Successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    // import

    public function supplier_from_list(Request $request, $id = null)
    {
        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];
        $staff = SmStaff::select('user_id', 'full_name')->wherein('company_id', $company_id)->get();
        $countries = SysCountries::all();
        $states = SysStates::all();
        try {
            //if($_POST){
            if (SysHelper::get_pagination_post($request)) {
                $customer_query = SysCustSupplForm::select('id', 'name', 'first_name', 'contcat_number', 'mobile', 'vat_number', 'email', 'created_at')->wherein('company_id', $company_id)->where('status', 1)->where('catid', 2);
                if ($request->company_name != "") {
                    $customer_query->where('name', 'like', '%' . $request->company_name . '%');
                }
                if ($request->contact_name != "") {
                    $customer_query->where('contcat_person', 'like', '%' . $request->contact_name . '%');
                }
                if ($request->email != "") {
                    $customer_query->where('email', 'like', '%' . $request->email . '%');
                }
                if ($request->vat_country != "") {
                    $customer_query->where('vat_country', $request->vat_country);
                }
                if ($request->vat_state != "") {
                    $customer_query->where('vat_state', $request->vat_state);
                }
            } else {
                $customer_query = SysCustSupplForm::select('id', 'name', 'first_name', 'contcat_number', 'mobile', 'vat_number', 'email', 'created_at')->wherein('company_id', $company_id)->where('status', 1)->where('catid', 2);
            }
            //$customer_query->wherein('r.created_by',$r[1]);

            $customer = $customer_query->paginate(30);


            $active_id = $id;
            $selectedSupp = [];

            $addSupplier = [];


            $action = false;
            $editData = [];

            if ($request->has('supplier_action')) {
                $poAction = $request->input('supplier_action');

                if ($poAction === 'add') {
                    $action = 'add';
                    $addSupplier = $this->getAddSupplier(); // Get data for adding supplier
                } elseif ($poAction === 'edit') {
                    $action = 'edit';
                    $editData = $this->supplier_form_edit($active_id); // Get all data for editing
                    // dd($editData);
                }
            } else {
                if ($id) {
                    $selectedSupp = $this->getSupplierData($id);
                } else {
                    $firstRecord = $customer->first();
                    if ($firstRecord) {
                        $active_id = $firstRecord->id;
                        $selectedSupp = $this->getSupplierData($firstRecord->id);
                    }
                }
            }

            return view('backEnd.cust-suppl.supplier_list_form', compact('customer', 'staff', 'countries', 'states', 'action', 'active_id', 'selectedSupp', 'addSupplier', 'editData'));

            /*$form_data = [
                'customer' => $customer,
                'staff' => $staff,
                'countries' => $countries,
                'states' => $states,
            ];
            session()->put('customer_list_query', $form_data);
            return redirect('customer');*/

            //return view('backEnd.cust-suppl.customer_list', compact('customer','staff','countries','states'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function supplier_form_edit($id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $countries = SysCountries::all();
            $vattype = SysVatType::all();
            $vat = SysVat::select('sys_vat.*', 'sys_countries.name')->join('sys_countries', 'sys_countries.id', 'sys_vat.vat_country')->wherein('company_id', $company_id)->where('status', 1)->get();
            $accounts = SysChartofAccounts::where('status', 1)->wherein('company_id', $company_id)->get();
            $accounttype = SysAccountType::all();
            $roles = Role::where('active_status', '=', '1')->where('id', 2)->get();
            $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();

            $supplier_type = SysSupplierType::where('status', '=', '1')->get();
            $purchase_type = SysPurchaseType::where('status', '=', '1')->get();

            $staffs = SysHelper::get_sales_persons2();
            $company = SysHelper::get_company_names();

            $editData = SysCustSupplForm::where('id', $id)->first();
            $editAddressbook = SysCustSupplAddressbookForm::where('cust_suppl_id', $id)->get();
            $editContact = SysCustSupplContactForm::where('cust_suppl_id', $id)->get();
            $editDoc = SysCustSupplDocForm::where('cust_suppl_id', $id)->get();
            $editAssign = DB::table('sys_cust_suppl_assign')->where('cust_supp_id', $id)->get();
            $row_id = $id;

            $designation = SmDesignation::select('id', 'title')->where('active_status', 1)->orderby('title', 'asc')->get();
            $department = SmHumanDepartment::select('id', 'name')->where('active_status', 1)->orderby('name', 'asc')->get();

            $a = $editData->name;
            $b = $editData->email;
            $excisting_list = SysCustSuppl::select('id', 'code', 'name', 'email', 'first_name', 'mobile', 'contcat_number')->where(function ($query) use ($a, $b) {
                $query->where('name', '=', $a)
                    ->orWhere('email', '=', $b);
            })->where('catid', 2)->get();
            //return $excisting_list;

            return compact('roles', 'paymentterms', 'staffs', 'accounts', 'accounttype', 'countries', 'vattype', 'supplier_type', 'purchase_type', 'vat', 'editData', 'editAddressbook', 'editContact', 'editDoc', 'editAssign', 'row_id', 'excisting_list', 'designation', 'department', 'company');
            // return view('backEnd.cust-suppl.editSupplierForm', compact('roles', 'paymentterms', 'staffs', 'accounts', 'accounttype', 'countries', 'vattype', 'supplier_type', 'purchase_type', 'vat', 'editData', 'editAddressbook', 'editContact', 'editDoc', 'editAssign', 'row_id', 'excisting_list', 'designation', 'department', 'company'));
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function supplier_form_approve(Request $request)
    {
        $input = $request->all();

        //return $input;
        $dom = explode("@", $request->email);
        //$check = SysCustSuppl::select('id','code','name')->where('email', $request->email)->wherenotin('email', ['x','xx','xxx','xxxx'])->first();
        if (SysHelper::check_supplier_is_added($request->customer_name) > 0) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

        try {
            $company_access = "";
            if ($request->company_access != "") {
                $company_access = implode(",", $request->company_access);
            }
            if (!in_array(1, $request->company_access)) {
                $company_access = '1,' . $company_access;
            }

            DB::beginTransaction();
            $new_customer = new SysCustSuppl();
            $new_customer->group = SysHelper::get_supplier_group('group');
            $new_customer->catid = 2;  // 1 customers, 2 suppliers
            $new_customer->customer_salutation = $request->customer_salutation;
            $new_customer->first_name = $request->first_name;
            $new_customer->designation = $request->designation;
            $new_customer->last_name = $request->last_name;
            $new_customer->name = $request->customer_name;
            $new_customer->customer_name_display = $request->customer_name_display;
            $new_customer->code = SysHelper::get_new_supplier_code();
            $new_customer->address = $request->address;
            $new_customer->address2 = $request->address2;
            $new_customer->contcat_person = $request->e_first_name[0];
            $new_customer->contcat_number = $request->mobile_code;
            $new_customer->mobile = $request->mobile;
            $new_customer->email = $request->email;
            $new_customer->sales_person = Auth::user()->id;
            //$new_customer->vat_type = $request->vat_type;
            $new_customer->supplier_type = $request->supplier_type;
            $new_customer->purchase_type = $request->purchase_type;
            $new_customer->vat_country = $request->country_vat;
            //$new_customer->vat_state = $request->state_vat;
            $new_customer->city = $request->city;
            $new_customer->zip_code = $request->zip_code;
            $new_customer->vat_percentage = $request->vat_percentage;
            $new_customer->vat_number = $request->vat_number;
            $new_customer->credit_limit = str_replace(',', '', $request->credit_limit) ?: null;
            $new_customer->credit_days = $request->credit_days ?: null;
            $new_customer->payment_terms = $request->payment_terms;
            $new_customer->transaction_type = $request->transaction_type;
            //$new_customer->customer_documents = $customer_documents;
            $new_customer->status = 1;
            if ($request->vat_percentage_fixed) {
                $new_customer->vat_is_fixed = 1;
            }
            $new_customer->created_by = Auth::user()->id;
            //$new_customer->created_at = '';
            $new_customer->type = $request->type;
            $new_customer->company_id = session('logged_session_data.company_id');
            $new_customer->company_access = $company_access;
            $results1 = $new_customer->save();


            $cart_address = SysCustSupplAddressbookForm::where('cust_suppl_id', $request->row_id)->get();
            if (count($cart_address) > 0) {
                foreach ($cart_address as $key) {
                    $address = new SysCustSupplAddressbook();
                    $address->cust_suppl_id = $new_customer->id;
                    $address->address = $key->address;
                    $address->address2 = $key->address2;
                    $address->city = $key->city;
                    $address->country = $key->country;
                    $address->state = $key->state;
                    $address->zip_code = $key->zip_code;
                    $address->set_default = $key->set_default;
                    $address->company_id = session('logged_session_data.company_id');
                    $address->is_shipping = $key->is_shipping;
                    $address->status = 1;
                    $address->created_by = Auth::user()->id;
                    $results = $address->save();
                }
            }

            for ($i = 0; $i < count($request->e_first_name); $i++) {
                if ($request->e_first_name[$i] != "" && $request->e_email_address[$i] != "" && ($request->e_work_phone[$i] != "" || $request->e_mobile[$i] != "")) {
                    $contact = new SysCustSupplContact();
                    $contact->cust_suppl_id = $new_customer->id;
                    $contact->salutation = $request->e_salutation[$i];
                    $contact->first_name = $request->e_first_name[$i];
                    $contact->last_name = $request->e_last_name[$i];
                    $contact->email_address = $request->e_email_address[$i];
                    $contact->work_phone = $request->e_work_phone[$i];
                    $contact->mobile = $request->e_mobile[$i];
                    $contact->designation = $request->e_designation[$i];
                    $contact->department = $request->e_department[$i];
                    $contact->status = 1;
                    $contact->set_default = 1;
                    $contact->company_id = session('logged_session_data.company_id');
                    $contact->created_by = Auth::user()->id;
                    $results = $contact->save();
                }
            }

            $cart_doc = SysCustSupplDocForm::where('cust_suppl_id', $request->row_id)->get();
            if (count($cart_doc) > 0) {
                foreach ($cart_doc as $key) {
                    DB::table('sys_cust_suppl_doc')->insert([
                        'cust_suppl_id' => $new_customer->id,
                        'doc_name' => $key->doc_name,
                        'doc_file' => $key->doc_file,
                        'doc_exp_date' => $key->doc_exp_date,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                    ]);
                }
            }

            $accounts = new SysChartofAccounts();
            $accounts->account_code = $new_customer->code;
            $accounts->account_name = $request->customer_name;
            $accounts->group = SysHelper::get_supplier_group('group');
            $accounts->subgroup = SysHelper::get_supplier_group('subgroup');
            $accounts->subgroup2 = SysHelper::get_supplier_group('subgroup2');
            $accounts->status = 1;
            $accounts->company_id = session('logged_session_data.company_id');
            $accounts->company_access = $company_access;
            $accounts->created_by = Auth::user()->id;
            $results = $accounts->save();


            SysCustSupplAddressbookForm::where('cust_suppl_id', $request->row_id)->delete();
            SysCustSupplContactForm::where('cust_suppl_id', $request->row_id)->delete();
            SysCustSupplDocForm::where('cust_suppl_id', $request->row_id)->delete();
            SysCustSupplForm::where('id', $request->row_id)->delete();


            session()->forget('subgroup2');
            DB::commit();

            if ($results) {
                Toastr::success('Operation successful', 'Success');
                return redirect('view-supplier/' . $new_customer->id);
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }



        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }
    public function supplier_form_delete($id)
    {
        try {
            SysCustSupplAddressbookForm::where('cust_suppl_id', $id)->delete();
            SysCustSupplContactForm::where('cust_suppl_id', $id)->delete();
            SysCustSupplDocForm::where('cust_suppl_id', $id)->delete();
            SysCustSupplForm::where('id', $id)->delete();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();

        } catch (\Throwable $th) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function supplier_form_merge($supl_id, $sub_id)
    {
        try {

            DB::beginTransaction();

            $subdata = SysCustSupplForm::where('id', $sub_id)->first();
            $subdata_address = SysCustSupplAddressbookForm::where('cust_suppl_id', $sub_id)->get();
            $subdata_contact = SysCustSupplContactForm::where('cust_suppl_id', $sub_id)->get();
            $subdata_doc = SysCustSupplDocForm::where('cust_suppl_id', $sub_id)->get();


            DB::table('sys_cust_suppl_addressbook')->where('cust_suppl_id', $supl_id)->update(['set_default' => 0]);
            DB::table('sys_cust_suppl_contact')->where('cust_suppl_id', $supl_id)->update(['set_default' => 0]);

            $new_customer = SysCustSuppl::find($supl_id);
            $new_customer->customer_salutation = $subdata->customer_salutation;
            $new_customer->first_name = $subdata->first_name;
            $new_customer->designation = $subdata->designation;
            $new_customer->last_name = $subdata->last_name;
            $new_customer->name = $subdata->name;
            $new_customer->customer_name_display = $subdata->customer_name_display;
            if (count($subdata_contact) > 0) {
                $new_customer->contcat_person = $subdata_contact[0]->first_name;
            }
            $new_customer->contcat_number = $subdata->mobile_code;
            $new_customer->mobile = $subdata->mobile;
            $new_customer->email = $subdata->email;
            $new_customer->sales_person = $new_customer->sales_person;
            //$new_customer->vat_type = $request->vat_type;
            $new_customer->supplier_type = $subdata->supplier_type;
            $new_customer->purchase_type = $subdata->purchase_type;
            $new_customer->vat_country = $subdata->country_vat;
            //$new_customer->vat_state = $request->state_vat;
            $new_customer->city = $subdata->city;
            $new_customer->zip_code = $subdata->zip_code;
            $new_customer->vat_percentage = $subdata->vat_percentage;
            $new_customer->vat_number = $subdata->vat_number;

            $new_customer->credit_limit = str_replace(',', '', $new_customer->credit_limit);
            $new_customer->credit_days = $new_customer->credit_days;
            $new_customer->payment_terms = $new_customer->payment_terms;
            $new_customer->payment_terms_txt = $new_customer->payment_terms_txt;
            $new_customer->transaction_type = $new_customer->transaction_type;

            //$new_customer->customer_documents = $customer_documents;
            $new_customer->status = 1;
            if ($subdata->vat_percentage_fixed) {
                $new_customer->vat_is_fixed = 1;
            }
            $new_customer->updated_by = Auth::user()->id;
            //$new_customer->created_at = '';
            $new_customer->type = $subdata->type;
            // $new_customer->company_id = session('logged_session_data.company_id');
            $results1 = $new_customer->save();

            if (count($subdata_address) > 0) {
                foreach ($subdata_address as $key) {
                    $address = new SysCustSupplAddressbook();
                    $address->cust_suppl_id = $new_customer->id;
                    $address->address = $key->address;
                    $address->address2 = $key->address2;
                    $address->city = $key->city;
                    $address->country = $key->country;
                    $address->state = $key->state;
                    $address->zip_code = $key->zip_code;
                    $address->set_default = $key->set_default;
                    $address->company_id = $key->company_id;
                    $address->is_shipping = $key->is_shipping;
                    $address->status = 1;
                    $address->created_by = Auth::user()->id;
                    $results = $address->save();
                }
            }


            if (count($subdata_contact) > 0) {
                foreach ($subdata_contact as $key) {
                    $contact = new SysCustSupplContact();
                    $contact->cust_suppl_id = $new_customer->id;
                    $contact->salutation = $key->salutation;
                    $contact->first_name = $key->first_name;
                    $contact->last_name = $key->last_name;
                    $contact->email_address = $key->email_address;
                    $contact->work_phone = $key->work_phone;
                    $contact->mobile = $key->mobile;
                    $contact->designation = $key->designation;
                    $contact->department = $key->department;
                    $contact->status = 1;
                    $contact->set_default = 1;
                    $contact->company_id = $key->company_id;
                    $contact->created_by = Auth::user()->id;
                    $results = $contact->save();
                }
            }

            if (count($subdata_doc) > 0) {
                foreach ($subdata_doc as $key) {
                    DB::table('sys_cust_suppl_doc')->insert([
                        'cust_suppl_id' => $new_customer->id,
                        'doc_name' => $key->doc_name,
                        'doc_file' => $key->doc_file,
                        'doc_exp_date' => $key->doc_exp_date,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                    ]);
                }
            }

            SysChartofAccounts::where('account_code', $new_customer->code)->update(['account_name' => $new_customer->name]);


            SysCustSupplAddressbookForm::where('cust_suppl_id', $sub_id)->delete();
            SysCustSupplContactForm::where('cust_suppl_id', $sub_id)->delete();
            SysCustSupplDocForm::where('cust_suppl_id', $sub_id)->delete();
            SysCustSupplForm::where('id', $sub_id)->delete();

            DB::commit();

            Toastr::success('Supplier updated successfully', 'Success');
            return redirect('suppliers/' . $new_customer->id);

        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function supplier_name(Request $request)
    {
        try {
            if ($request->get('query')) {
                $company_name = $request->get('query');
                $data = DB::table('sys_cust_suppl')->select('name')->where('catid', 2)
                    ->where(function ($query) use ($company_name) {
                        $query->orwhere('name', 'like', '%' . $company_name . '%')
                            ->orwhere('name', 'like', '%' . str_replace(',', '', $company_name) . '%')
                            ->orwhere('name', 'like', '%' . str_replace(',', ' ', $company_name) . '%')
                            ->orwhere('name', 'like', '%' . str_replace('.', '', $company_name) . '%')
                            ->orwhere('name', 'like', '%' . str_replace('.', ' ', $company_name) . '%');
                    })->get();

                $output = '<ul class="form-control" style="list-style: none; height: auto; position: absolute; z-index: 999; line-height: 25px;">';
                foreach ($data as $row) {
                    $output .= '<li><a href="#">' . $row->name . '</a></li>';
                }
                $output .= '</ul>';
                echo $output;
            }
        } catch (\Throwable $th) {
            //return $th;
        }
    }

    function supplierMerge(Request $request)
    {
        try {
            DB::beginTransaction();

            /*$duplicates = DB::table('sys_cust_suppl')
            ->select('id', 'name', 'code')
            ->whereIn('code', function ($q){
                        $q->select('code')
                        ->from('sys_cust_suppl')
                        ->groupBy('code')
                        ->havingRaw('COUNT(*) > 1');
            })->where('company_id',1)->get();
            //return  $duplicates;
            if(count($duplicates)){
                foreach($duplicates as $dup){
                    $code = SysHelper::get_new_customer_code();
                    DB::table('sys_cust_suppl')->where('code',$dup->code)->where('company_id',1)->update(['code' => $code]);
                    DB::table('sys_chartofaccounts')->where('account_code',$dup->code)->where('company_id',1)->update(['account_code' => $code, 'account_name' => $dup->name]);
                }
            }*/

            $from_account = $request->from_account;
            $to_account = $request->to_account;

            if (count($from_account) > 0) {
                foreach ($from_account as $f_account) {
                    $from_cust = DB::table('sys_cust_suppl as c')->select('c.id', 'c.sales_person', 'c.company_access')->join('sys_chartofaccounts as a', 'a.account_code', 'c.code')->where('a.id', $f_account)->get();
                    $to_cust = DB::table('sys_cust_suppl as c')->select('c.id', 'c.sales_person', 'c.company_access')->join('sys_chartofaccounts as a', 'a.account_code', 'c.code')->where('a.id', $to_account)->get();
                    if (count($from_cust) > 1) {
                        DB::rollBack();
                        Toastr::error('Operation Failed. Multipple Account Code', 'Failed');
                        return redirect()->back();
                    }
                    if (count($to_cust) > 1) {
                        DB::rollBack();
                        Toastr::error('Operation Failed. Multipple Account Code', 'Failed');
                        return redirect()->back();
                    }

                    DB::table('sys_crm_deals')->where('cust_id', $from_cust[0]->id)->update(['cust_id' => $to_cust[0]->id]);
                    DB::table('sys_crm_leads')->where('cust_id', $from_cust[0]->id)->update(['cust_id' => $to_cust[0]->id]);

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

                    DB::table('sys_crm_amc_table')->where('cust_name', $from_cust[0]->id)->update(['cust_name' => $to_cust[0]->id]);
                    DB::table('sys_crm_ps_service_table')->where('cust_name', $from_cust[0]->id)->update(['cust_name' => $to_cust[0]->id]);

                    DB::table('sys_cust_suppl')->where('id', $from_cust[0]->id)->update(['status' => 2]);
                    DB::table('sys_chartofaccounts')->where('id', $f_account)->update(['status' => 2]);

                    DB::table('sys_cust_suppl_assign')->insert(['cust_supp_id' => $to_cust[0]->id, 'user_id' => $to_cust[0]->sales_person, 'type' => 1]);

                    $str = $from_cust[0]->company_access . ',' . $to_cust[0]->company_access;
                    $exploded = explode(',', $str);
                    $unique = array_unique($exploded);
                    $company_access = implode(',', $unique);

                    DB::table('sys_cust_suppl')->where('id', $to_account)->update(['company_access' => $company_access, 'status' => 1]);
                    DB::table('sys_chartofaccounts')->where('id', $to_account)->update(['company_access' => $company_access, 'status' => 1]);
                }
            }
            DB::commit();
            Toastr::success('Supplier Merged Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function supplierMergeDuplicate(Request $request)
    {
        try {
            if (count($request->duplicate_name) > 0) {
                DB::beginTransaction();
                foreach ($request->duplicate_name as $dup_code) {

                    $duplicate_customer = DB::table('sys_chartofaccounts')
                        ->select('id', 'account_name', 'account_code')
                        ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(account_name)), ' ', ''), '@', ''), '#', ''), '.', ''), '-', ''), '_', ''), '(', ''), ')', '') = ?", [$dup_code])
                        ->where('subgroup2', 19)->where('status', 1)->orderby('company_id', 'asc')
                        ->get();

                    if (count($duplicate_customer) > 0) {
                        $to_account = $duplicate_customer[0]->id;
                        for ($i = 1; $i < count($duplicate_customer); $i++) {
                            $f_account = $duplicate_customer[$i]->id;

                            $from_cust = DB::table('sys_cust_suppl as c')->select('c.id', 'c.sales_person', 'c.company_access')->join('sys_chartofaccounts as a', 'a.account_code', 'c.code')->where('a.id', $f_account)->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(name)), ' ', ''), '@', ''), '#', ''), '.', ''), '-', ''), '_', ''), '(', ''), ')', '') = ?", [$dup_code])->get();
                            $to_cust = DB::table('sys_cust_suppl as c')->select('c.id', 'c.sales_person', 'c.company_access')->join('sys_chartofaccounts as a', 'a.account_code', 'c.code')->where('a.id', $to_account)->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(name)), ' ', ''), '@', ''), '#', ''), '.', ''), '-', ''), '_', ''), '(', ''), ')', '') = ?", [$dup_code])->get();

                            if (count($from_cust) == 0 || count($to_cust) == 0) {
                                DB::rollBack();
                                Toastr::error('Operation Failed. Account Issue Found', 'Failed');
                                return redirect()->back();
                            }
                            if (count($from_cust) > 1) {
                                DB::rollBack();
                                Toastr::error('Operation Failed. Multipple Account Code', 'Failed');
                                return redirect()->back();
                            }
                            if (count($to_cust) > 1) {
                                DB::rollBack();
                                Toastr::error('Operation Failed. Multipple Account Code', 'Failed');
                                return redirect()->back();
                            }

                            DB::table('sys_crm_deals')->where('cust_id', $from_cust[0]->id)->update(['cust_id' => $to_cust[0]->id]);
                            SysHelper::cust_suppl_merge(2, 'sys_crm_deals', $from_cust[0]->id, $to_cust[0]->id);

                            DB::table('sys_crm_leads')->where('cust_id', $from_cust[0]->id)->update(['cust_id' => $to_cust[0]->id]);
                            SysHelper::cust_suppl_merge(2, 'sys_crm_leads', $from_cust[0]->id, $to_cust[0]->id);

                            DB::table('sys_item_stock')->where('account_id', $f_account)->update(['account_id' => $to_account]);
                            SysHelper::cust_suppl_merge(2, 'sys_item_stock', $f_account, $to_account);

                            DB::table('sys_chartofaccounts_transaction')->where('account_id', $f_account)->update(['account_id' => $to_account]);
                            SysHelper::cust_suppl_merge(2, 'sys_chartofaccounts_transaction', $f_account, $to_account);

                            DB::table('sys_proforma_invoice')->where('customer', $f_account)->update(['customer' => $to_account]);
                            SysHelper::cust_suppl_merge(2, 'sys_proforma_invoice', $f_account, $to_account);

                            DB::table('sys_sales_invoice')->where('customer', $f_account)->update(['customer' => $to_account]);
                            SysHelper::cust_suppl_merge(2, 'sys_sales_invoice', $f_account, $to_account);

                            DB::table('sys_delivery_note')->where('customer_id', $f_account)->update(['customer_id' => $to_account]);
                            SysHelper::cust_suppl_merge(2, 'sys_delivery_note', $f_account, $to_account);

                            DB::table('sys_sales_return')->where('customer', $f_account)->update(['customer' => $to_account]);
                            SysHelper::cust_suppl_merge(2, 'sys_sales_return', $f_account, $to_account);

                            DB::table('sys_receipt_adjustments')->where('account_id', $f_account)->update(['account_id' => $to_account]);
                            SysHelper::cust_suppl_merge(2, 'sys_receipt_adjustments', $f_account, $to_account);

                            DB::table('sys_purchase_order')->where('vendors', $f_account)->update(['vendors' => $to_account]);
                            SysHelper::cust_suppl_merge(2, 'sys_purchase_order', $f_account, $to_account);

                            DB::table('sys_purchase_grn')->where('vendors', $f_account)->update(['vendors' => $to_account]);
                            SysHelper::cust_suppl_merge(2, 'sys_purchase_grn', $f_account, $to_account);

                            DB::table('sys_purchase_invoice')->where('vendors', $f_account)->update(['vendors' => $to_account]);
                            SysHelper::cust_suppl_merge(2, 'sys_purchase_invoice', $f_account, $to_account);

                            DB::table('sys_purchase_return')->where('vendors', $f_account)->update(['vendors' => $to_account]);
                            SysHelper::cust_suppl_merge(2, 'sys_purchase_return', $f_account, $to_account);

                            DB::table('sys_payment_adjustments')->where('account_id', $f_account)->update(['account_id' => $to_account]);
                            SysHelper::cust_suppl_merge(2, 'sys_payment_adjustments', $f_account, $to_account);

                            DB::table('sys_crm_amc_table')->where('cust_name', $from_cust[0]->id)->update(['cust_name' => $to_cust[0]->id]);
                            SysHelper::cust_suppl_merge(2, 'sys_crm_amc_table', $from_cust[0]->id, $to_cust[0]->id);

                            DB::table('sys_crm_ps_service_table')->where('cust_name', $from_cust[0]->id)->update(['cust_name' => $to_cust[0]->id]);
                            SysHelper::cust_suppl_merge(2, 'sys_crm_ps_service_table', $from_cust[0]->id, $to_cust[0]->id);

                            DB::table('sys_cust_suppl')->where('id', $from_cust[0]->id)->update(['status' => 2]);
                            DB::table('sys_chartofaccounts')->where('id', $f_account)->update(['status' => 2]);

                            DB::table('sys_cust_suppl_assign')->insert(['cust_supp_id' => $to_cust[0]->id, 'user_id' => $to_cust[0]->sales_person, 'type' => 1]);

                            $str = $from_cust[0]->company_access . ',' . $to_cust[0]->company_access;
                            $exploded = explode(',', $str);
                            $unique = array_unique($exploded);
                            $company_access = implode(',', $unique);

                            DB::table('sys_cust_suppl')->where('id', $to_account)->update(['company_access' => $company_access, 'status' => 1]);
                            DB::table('sys_chartofaccounts')->where('id', $to_account)->update(['company_access' => $company_access, 'status' => 1]);

                        }
                    }
                }
                DB::commit();
                Toastr::success('Supplier Merged Successfully', 'Success');
                return redirect()->back();
            } else {
                Toastr::error('No Supplier Selected', 'Failed');
                return redirect()->back();
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function search(Request $request)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $com_id = session('logged_session_data.company_id');
            $q = $request->get('query');
            $formattedDate = null;
            if (preg_match('/\d{2}[\/\-]\d{2}[\/\-]\d{4}/', $q)) {
                $normalized = str_replace('/', '-', $q);
                $formattedDate = date('Y-m-d', strtotime($normalized));
            }

            // Build the base query
            $suppliers = SysCustSuppl::where('catid', 2) // Only suppliers
                ->whereRaw("find_in_set($com_id, company_access)")
                ->where(function ($query) use ($q, $formattedDate) {
                    $query->where('code', 'like', "%{$q}%")
                        ->orWhere('name', 'like', "%{$q}%")
                        ->orWhere('contcat_person', 'like', "%{$q}%")
                        ->orWhere('contcat_number', 'like', "%{$q}%")
                        ->orWhere('vat_number', 'like', "%{$q}%")
                        ->orWhere('mobile', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");

                    // If a date is detected, also search in created_at
                    if ($formattedDate) {
                        $query->orWhereDate('created_at', $formattedDate);
                    }
                })
                ->orderby('name', 'asc')
                ->limit(100) // Limit results
                ->get();


            return response()->json($suppliers);
        } catch (\Throwable $th) {
            return $th;
        }
    }




    
}
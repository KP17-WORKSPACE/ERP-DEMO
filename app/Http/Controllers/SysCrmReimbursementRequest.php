<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SmInspectingDepartment;
use App\SmItem;
use Illuminate\Http\Request;
use App\SmItemStore;
use App\SmStaff;
use App\SysBrand;
use App\SysChartofAccounts;
use App\SysCompany;
use App\SysCountries;
use App\SysCrmAmc;
use App\SysCrmDeals;
use App\SysCrmDealsCollaboration;
use App\SysCrmDealsComments;
use App\SysCrmDealTrack;
use App\SysCrmLeads;
use App\SysCrmQuoteCSItems;
use App\SysCrmQuoteItems;
use App\SysCrmReimbursement;
use App\SysCrmService;
use App\SysCrmServiceAssign;
use App\SysCrmServiceComments;
use App\SysCurrencySettings;
use App\SysCustSuppl;
use App\SysCustSupplAddressbook;
use App\SysHelper;
use App\SysItemOpeningStock;
use App\SysItemStock;
use App\SysPaymentTerms;
use App\SysShipping;
use App\SysStockIn;
use App\SysStockInSerialNo;
use App\SysSupplierType;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;
use Validator;

class SysCrmReimbursementRequest extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function index(Request $request)
    {
        try {
            $company_id = session('logged_session_data.company_id');

            if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2) {
                $data = SysCrmReimbursement::where('company_id', $company_id)->orderby('date', 'desc')->get();
            } elseif (Auth::user()->role_id == 28) { //accounts_status
                $data = SysCrmReimbursement::where('company_id', $company_id)->where('dept_head_status', 1)->where('acco_head_status', 1)->where('accounts_status', 0)->orderby('date', 'desc')->get();
            } elseif (Auth::user()->role_id == 27) { //acco_head_status
                $data = SysCrmReimbursement::where('company_id', $company_id)->where('dept_head_status', 1)->where('acco_head_status', 0)->where('accounts_status', 0)->orderby('date', 'desc')->get();
            } elseif (Auth::user()->role_id == 8) { //dept_head_status
                //$users = SmStaff::->pluck('user_id');
                $data = SysCrmReimbursement::where('company_id', $company_id)->where('dept_head_status', 0)->where('acco_head_status', 0)->where('accounts_status', 0)->orderby('date', 'desc')->get();
            } else {
                $data = SysCrmReimbursement::where('company_id', $company_id)->where('created_by', Auth::user()->id)->orderby('date', 'desc')->get();
            }
            
            $currencies = \App\SysCurrency::where('active_status', 1)->get();
            $company = \App\SysCompany::find($company_id);
            $default_currency = $company ? $company->currency_id : null;
            
            $last = SysCrmReimbursement::orderBy('id', 'desc')->first();
            $next_id = $last ? $last->id + 1 : 1;
            $next_reimbursement_no = 'REIM-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);

            return view('backEnd.amc.reimbursementlist', compact('data', 'currencies', 'default_currency', 'next_reimbursement_no'));

        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
    public function store(Request $request)
    {
        
        
        try {
            $doc_file = "";
            if ($request->file('attachmant') != "") {
                $files = $request->file('attachmant');
                for ($i = 0; $i < count($files); $i++) {
                    $file1 = $files[$i];
                    $doc_file = md5(time()) . "_reimbursement_" . $i . "." . $file1->getclientoriginalextension();
                    $file1->move('public/uploads/crm_amc_doc/', $doc_file);
                    $lpo[] = $doc_file;
                }
                $doc_file = implode("|", $lpo);
            }
            $r = new SysCrmReimbursement();
            
            $last = SysCrmReimbursement::orderBy('id', 'desc')->first();
            $next_id = $last ? $last->id + 1 : 1;
            $r->reimbursement_no = 'REIM-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
            
            $r->date = $request->date
                ? Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d')
                : null;
                
            $r->deal_id = SysHelper::get_dealid_from_code($request->deal_id);
            $r->site_name = $request->site_name;
            $r->scope_of_work = $request->scope_of_work;
            $r->invoice_no = $request->invoice_no;
            $r->amount = $request->amount;
            if ($request->remarks == "Other") {
                $r->remarks = $request->remarks_other;
            } else {
                $r->remarks = $request->remarks;
            }
            $r->head_count_name = $request->head_count_name;
            $r->invoice_date = $request->invoice_date ? Carbon::createFromFormat('d/m/Y', $request->invoice_date)->format('Y-m-d') : null;
            $r->reimbursable_amount = $request->reimbursable_amount;
            $r->payment_method = $request->payment_method;
            $r->project_id = $request->project_id;
            $r->vendor_name = $request->vendor_name;
            $r->currency_id = $request->currency_id;
            $r->attachmant = $doc_file;
            $r->created_by = Auth::user()->id;
            $r->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $r->company_id = session('logged_session_data.company_id');
            $r->status = 1;
            $r->save();
            Toastr::success('Reimbursement has been added successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public function update(Request $request)
    {
        
        try {
            $doc_file = "";
            if ($request->file('attachmant') != "") {
                $files = $request->file('attachmant');
                for ($i = 0; $i < count($files); $i++) {
                    $file1 = $files[$i];
                    $doc_file = md5(time()) . "_reimbursement_" . $i . "." . $file1->getclientoriginalextension();
                    $file1->move('public/uploads/crm_amc_doc/', $doc_file);
                    $lpo[] = $doc_file;
                }
                $doc_file = implode("|", $lpo);
            }
            
            $r = SysCrmReimbursement::find($request->edit_id);
            $r->date = $request->date
                ? Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d')
                : null;
            $r->deal_id = SysHelper::get_dealid_from_code($request->deal_id);
            $r->site_name = $request->site_name;
            $r->scope_of_work = $request->scope_of_work;
            $r->invoice_no = $request->invoice_no;
            $r->amount = $request->amount;
            if ($request->remarks == "Other") {
                $r->remarks = $request->remarks_other;
            } else {
                $r->remarks = $request->remarks;
            }
            $r->head_count_name = $request->head_count_name;
            $r->invoice_date = $request->invoice_date ? Carbon::createFromFormat('d/m/Y', $request->invoice_date)->format('Y-m-d') : null;
            $r->reimbursable_amount = $request->reimbursable_amount;
            $r->payment_method = $request->payment_method;
            $r->project_id = $request->project_id;
            $r->vendor_name = $request->vendor_name;
            $r->currency_id = $request->currency_id;
            if ($doc_file != "") {
                $r->attachmant = $doc_file;
            }
            $r->updated_by = Auth::user()->id;
            $r->updated_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');

            if ($r->dept_head_status == 2) {
                $r->dept_head_status = 0;
            }
            if ($r->acco_head_status == 2) {
                $r->acco_head_status = 0;
            }
            if ($r->accounts_status == 2) {
                $r->accounts_status = 0;
            }

            $r->save();
            Toastr::success('Reimbursement has been updated successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function account_approve(Request $request)
    {
        try {
            $r = SysCrmReimbursement::find($request->account_re_id);
            if (!$r) return redirect()->back();
            
            // Backend validation
            if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && $r->acco_head_status != 1) {
                Toastr::error('Finance must approve first.', 'Failed');
                return redirect()->back();
            }
            if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && $r->accounts_status == 1) {
                Toastr::error('Approved record cannot be edited.', 'Failed');
                return redirect()->back();
            }

            $r->accounts_status = $request->btn_status;
            $r->accounts_by = Auth::user()->id;
            $r->accounts_remarks = $request->remarks;
            
            if ($request->btn_status == 1) {
                $r->accounts_payment_voucher_no = $request->accounts_payment_voucher_no;
                $r->accounts_payment_date = $request->accounts_payment_date ? Carbon::createFromFormat('d/m/Y', $request->accounts_payment_date)->format('Y-m-d') : null;
                $r->accounts_payment_method = $request->accounts_payment_method;
                $r->accounts_bank_account_id = $request->accounts_bank_account_id;
                $r->accounts_paid_amount = $request->accounts_paid_amount;
                $r->accounts_payment_status = $request->accounts_payment_status;
                $r->accounts_payment_reference = $request->accounts_payment_reference;
            }

            $r->save();

            if ($request->btn_status == 1) {
                Toastr::success('Approved has been added successfully', 'Success');
            } else {
                Toastr::warning('Disapproved has been added successfully', 'Warning');
            }
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
    public function accounts_head_approve(Request $request)
    {
        try {
            $r = SysCrmReimbursement::find($request->acco_head_re_id);
            if (!$r) return redirect()->back();

            // Backend validation
            if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && $r->dept_head_status != 1) {
                Toastr::error('Reporting Manager must approve first.', 'Failed');
                return redirect()->back();
            }
            if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && $r->acco_head_status == 1) {
                Toastr::error('Approved record cannot be edited.', 'Failed');
                return redirect()->back();
            }

            $r->acco_head_status = $request->btn_status;
            $r->acco_head_by = Auth::user()->id;
            $r->acco_head_remarks = $request->remarks;
            
            if ($request->btn_status == 1) {
                $r->acco_head_approved_amount = $request->acco_head_approved_amount;
                $r->acco_head_account_id = $request->acco_head_account_id;
                $r->acco_head_payment_required = $request->acco_head_payment_required;
            }

            $r->save();

            if ($request->btn_status == 1) {
                Toastr::success('Approved has been added successfully', 'Success');
            } else {
                Toastr::warning('Disapproved has been added successfully', 'Warning');
            }
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
    public function dept_head_approve(Request $request)
    {
        try {
            $r = SysCrmReimbursement::find($request->dept_head_re_id);
            if (!$r) return redirect()->back();

            if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && $r->dept_head_status == 1) {
                Toastr::error('Approved record cannot be edited.', 'Failed');
                return redirect()->back();
            }

            $r->dept_head_status = $request->btn_status;
            $r->dept_head_by = Auth::user()->id;
            $r->dept_head_remarks = $request->remarks;
            
            if ($request->btn_status == 1) {
                $r->dept_head_date = $request->dept_head_date ? Carbon::createFromFormat('d/m/Y', $request->dept_head_date)->format('Y-m-d') : null;
            }

            $r->save();

            if ($request->btn_status == 1) {
                Toastr::success('Approved has been added successfully', 'Success');
            } else {
                Toastr::warning('Disapproved has been added successfully', 'Warning');
            }
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }


    public function get_custname(Request $request)
    {
        try {
            $deal_id = SysHelper::get_dealid_from_code($request->deal_id);

            $customers = DB::table('sys_cust_suppl as cs')->select('cs.name')
                ->leftjoin('sys_crm_deals as d', 'd.cust_id', 'cs.id')
                ->where('d.id', $deal_id)
                ->limit(1)->orderby('cs.id', 'desc')->get();
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
    public function delete(Request $request)
    {
        try {
            $r = SysCrmReimbursement::find($request->id);
            $r->status = 2;
            $r->save();
            $bug = 0;
        } catch (\Exception $e) {
            return $e;
            $bug = $e->errorInfo[1];
        }
        if ($bug == 0) {
            return json_encode("SUCCESS");
        } else {
            $retData = 'ERROR';
            return json_encode('ERROR');
        }
    }
    public function restore(Request $request)
    {
        try {
            $r = SysCrmReimbursement::find($request->id);
            $r->status = 1;
            $r->save();
            $bug = 0;
        } catch (\Exception $e) {
            return $e;
            $bug = $e->errorInfo[1];
        }
        if ($bug == 0) {
            return json_encode("SUCCESS");
        } else {
            $retData = 'ERROR';
            return json_encode('ERROR');
        }
    }
    
    public function track(Request $request, $id = null)
    {
        try {
            $query = SysCrmReimbursement::where('status', '!=', 2);
            $company_id = session('logged_session_data.company_id');
            if ($company_id != 1) {
                $query->where('company_id', $company_id);
            }

            if ($request->has('reimbursement_no') && $request->reimbursement_no != '') {
                $query->where('reimbursement_no', 'like', '%' . $request->reimbursement_no . '%');
            }
            if ($request->has('vendor_name') && $request->vendor_name != '') {
                $query->where('vendor_name', 'like', '%' . $request->vendor_name . '%');
            }
            if ($request->has('from_date') && $request->from_date != '') {
                $query->whereDate('date', '>=', \Carbon\Carbon::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d'));
            }
            if ($request->has('to_date') && $request->to_date != '') {
                $query->whereDate('date', '<=', \Carbon\Carbon::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d'));
            }

            $data = $query->orderBy('id', 'desc')->get();

            $ctrl_reimbursement_no = $request->reimbursement_no;
            $ctrl_vendor_name = $request->vendor_name;
            $ctrl_from_date = $request->from_date;
            $ctrl_to_date = $request->to_date;
            
            $active_id = $id;
            $selectedReimbursement = null;
            if ($active_id == null) {
                $firstRecord = $data->first();
                if ($firstRecord) {
                    $active_id = $firstRecord->id;
                    $selectedReimbursement = $firstRecord;
                }
            } else {
                $selectedReimbursement = SysCrmReimbursement::find($active_id);
            }
            
            $staff = null;
            if ($selectedReimbursement) {
                $staff = \App\SmStaff::where('user_id', $selectedReimbursement->created_by)->first();
            }
            
            return view('backEnd.amc.reimbursement_track', compact('data', 'active_id', 'selectedReimbursement', 'staff', 'ctrl_reimbursement_no', 'ctrl_vendor_name', 'ctrl_from_date', 'ctrl_to_date'));
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function search(Request $request)
    {
        try {
            $company_id = session('logged_session_data.company_id');
            $query = SysCrmReimbursement::where('status', '!=', 2);
            if ($company_id != 1) {
                $query->where('company_id', $company_id);
            }

            if ($request->has('query')) {
                $search = $request->input('query');
                $query->where(function ($q) use ($search) {
                    $q->where('reimbursement_no', 'LIKE', "%{$search}%")
                        ->orWhere('vendor_name', 'LIKE', "%{$search}%");
                });
            }

            $data = $query->orderBy('id', 'desc')->get();

            $formatted_data = $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'reimbursement_no' => $item->reimbursement_no,
                    'customer_name' => $item->deal_code->customername->name ?? '',
                    'date' => $item->date,
                    'amount' => $item->amount,
                    'currency_code' => $item->currencycode->code ?? ''
                ];
            });

            return response()->json($formatted_data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getDetails($id)
    {
        try {
            $selectedReimbursement = SysCrmReimbursement::find($id);
            if (!$selectedReimbursement) {
                return response()->json(['error' => 'Not found'], 404);
            }
            $staff = \App\SmStaff::where('user_id', $selectedReimbursement->created_by)->first();

            return view('backEnd.amc.reimbursement_track_detail', compact('selectedReimbursement', 'staff'))->render();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
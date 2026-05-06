<?php

namespace App\Http\Controllers;

use App\SysChartofAccounts;
use App\SysAccountType;
use App\ApiBaseMethod;
use App\SmItem;
use App\SysBankPayment;
use App\SysCashReceiptList;
use App\SysCustSuppl;
use App\SysAccountGroup;
use App\SysChartofAccountsDetails;
use App\SysChartofAccountsTransaction;
use App\SysCompany;
use App\SysCurrencySettings;
use App\SysHelper;
use App\SysLedgerEntries;
use App\SysLedgerEntriesTemp;
use App\SysPayment;
use App\SysPaymentAdjustments;
use App\SysPaymentAdjustmentsTemp;
use App\SysPaymentCheque;
use App\SysPurchaseInvoice;
use App\SysPurchaseOrder;
use App\SysReceipt;
use App\SysReceiptAdjustments;
use App\SysReceiptAdjustmentsTemp;
use App\SysReceiptMode;
use App\SysSalesInvoice;
use App\SysSalesInvoiceCFCharges;
use App\SysSTL;
use App\SysSTLItems;
use App\SysSTLPayment;
use App\SysTransactions;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;
use PayPal\Api\Currency;

class SysSTLController extends Controller
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
    public function index(Request $request, $id = null)
    {
        try {
            $company_id = session('logged_session_data.company_id');
            $stl = SysSTL::where('company_id', $company_id)->orderby('id', 'desc')->get();

            $selectedId = null;
            if ($id !== null && $stl->where('id', $id)->count() > 0) {
                $selectedId = $id;
            } elseif (count($stl) > 0) {
                $selectedId = $stl[0]->id;
            }

            $data = [];
            if ($selectedId) {
                $data = $this->get_stl_pdf_data($selectedId);
            }

            return view('backEnd.stl.stl_list', compact('stl', 'data'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function getDetails($id)
    {
        $data = $this->get_stl_pdf_data($id);
        if (count($data) > 0) {
            return view('backEnd.stl.s_details', $data);
        } else {
            return "error!!";
        }

    }

    public function get_stl_pdf_data($id)
    {
        try {
            $stl = SysSTL::find($id);
            $vendorDet = SysCustSuppl::where('code', $stl->vendor_name->account_code)->first();
            $stlItems = SysSTLItems::where('stl_id', $id)->get();
            if ($stl->pi_no == 1) {
                $tax_invoices = "Purchase Invoice";
            } elseif ($stl->pi_no == 2) {
                $tax_invoices = "Proforma Invoice";
            } else {
                $tax_invoices = "Purchase Order";
            }
            $invoices_no = $stlItems->pluck('pi_inv_no')->unique()->implode(', ');

            $with_with_out = $stl->with_amount;

            $company = SysCompany::find($stl->company_id);
            $print = date('d/m/Y h:i A', strtotime(Carbon::now('+04:00')));

            $data = [
                'company' => $company,
                'stl' => $stl,
                'stl_items' => $stlItems,
                'vendor_det' => $vendorDet,
                'print' => $print,
                'tax_invoices' => $tax_invoices,
                'invoices_no' => $invoices_no,
                'with_with_out' => $with_with_out,
            ];
            return $data;
        } catch (\Throwable $th) {
            return [];
        }
    }

    public function add(Request $request, $page_id = null)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $stl = SysSTL::where('company_id', $company_id)->orderby('id', 'desc')->get();
            $company = SysCompany::where('id', session('logged_session_data.company_id'))->first();
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $currency_code = $currency->where('id', $company->currency_id)->first();
            $currency_code = $currency_code->code;
            $vendor = SysHelper::get_stl_supplier_list($company_id);
            $bank = SysHelper::get_stl_bank_account();
            $product = DB::table('sm_items as items')->select('items.id', 'items.part_number', 'items.description', 'cat.category_name as cat_name')
                ->join('sm_item_categories as cat', 'cat.id', 'items.category_name')->where('items.status', 1)->orderby('items.part_number', 'asc')->get();
                
            return view('backEnd.stl.stl_add', compact('vendor', 'bank', 'product', 'company', 'currency', 'currency_code', 'stl'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    function getpiforstl(Request $request)
    {
        try {
            $ret = SysPurchaseInvoice::where('vendors', $request->id)->get();
            //->where('company_id',session('logged_session_data.company_id'))
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
    function getpoforstl(Request $request)
    {
        try {
            $ret = SysPurchaseOrder::where('vendors', $request->id)->get();
            //->where('company_id',session('logged_session_data.company_id'))
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
    function getpilistforstl(Request $request)
    {
        try {
            $ret = DB::table('sys_purchase_invoice_items as pi_items')->select('pi_items.*', 'cat.category_name as cat_name', 'pi.doc_number', 'pi.pi_date', 'pi.currency', 'pi.awbno', 'pi.lpo_number', 'pi.lpo_date', 'pi.bill_number', 'pi.bill_date', 'pi.payment_terms', 'pi.payment_terms2', 'pi.reference', 'pi.location', 'pi.warehouse', 'pi.salesman_name', 'pi.narration', 'pi.ref_po_id', 'pi.ref_grn_id', 'items.part_number as part_number_txt', 'items.description')
                ->join('sys_purchase_invoice as pi', 'pi.id', 'pi_items.pi_id')
                ->join('sm_items as items', 'items.id', 'pi_items.part_number')
                ->join('sm_item_categories as cat', 'cat.id', 'items.category_name')
                ->where('pi_items.pi_id', $request->pi_id)->where('pi.status', 1)->get();
            return response()->json([$ret]);
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }
    function getpolistforstl(Request $request)
    {
        try {
            $ret = DB::table('sys_purchase_order_items as po_items')->select('po_items.*', 'cat.category_name as cat_name', 'po.doc_number', 'po.po_date', 'po.currency', 'po.awbno', 'po.boeno', 'po.payment_terms', 'po.payment_terms2', 'po.reference', 'po.salesman_name', 'po.narration', 'items.part_number as part_number_txt', 'items.description')
                ->join('sys_purchase_order as po', 'po.id', 'po_items.po_id')
                ->join('sm_items as items', 'items.id', 'po_items.part_number')
                ->join('sm_item_categories as cat', 'cat.id', 'items.category_name')
                ->where('po_items.po_id', $request->po_id)->where('po.status', 1)->get();
            return response()->json([$ret]);
        } catch (\Exception $e) {
            $ret = $e;
            return json_encode(array('data' => $ret));
        }
    }

    public function store(Request $request)
    {
        try {
           
            DB::beginTransaction();

            $stl = new SysSTL();
            $stl->doc_number = SysHelper::get_new_code('sys_stl', 'STL', 'doc_number');
            $stl->doc_date = SysHelper::normalizeToYmd($request->doc_date);
            $stl->bank = $request->bank;
            $stl->exchange_rate = $request->exchange_rate;
            $stl->amount_usd = floatval(str_replace(',', '', $request->amount_usd));
            $stl->amount_aed = floatval(str_replace(',', '', $request->amount_aed));
            $stl->currency = $request->currency;
            $stl->currency_m = $request->currency_m;
            $stl->owner_name = $request->owner_name;
            $stl->bank_representative = $request->bank_representative;
            $stl->vendor = $request->vendor;
            $stl->payment_type = $request->payment_type;
            $stl->pi_no = $request->pi_no;
            $stl->submition_date = SysHelper::normalizeToYmd($request->submition_date);
            $stl->narration = $request->narration;
            $stl->partial_remarks = $request->partial_remarks;
            $stl->with_amount = $request->with_amount;
            $stl->status = 1;
            $stl->created_by = Auth::user()->id;
            $stl->created_at = Carbon::now('+04:00');
            $stl->company_id = session('logged_session_data.company_id');
            $results = $stl->save();
            $stl->toArray();

            for ($i = 0; $i < count($request->partno); $i++) {
                if ($request->pi_no == 1) {
                    $pi_no = $request->purchase_inv[$i];
                } else {
                    $pi_no = 0;
                }

                if ($request->partno[$i] == 0) {
                    $partno = db::table('sm_items')->select('id')->where('part_number', $request->part_number[$i])->where('status', 1)->first();
                    if (isset($partno)) {
                        $partno = $partno->id;
                    } else {
                        $partno = 0;
                    }
                } else {
                    $partno = $request->partno[$i];
                }

                $temp_data[] = [
                    'stl_id' => $stl->id,
                    'pi_no' => $pi_no,
                    'part_id' => $request->partno[$i],
                    'part_no' => $request->part_number[$i],
                    'description' => $request->description[$i],
                    'amount' => floatval(str_replace(',', '', $request->amount[$i])),
                    'status' => 1,
                    'pi_inv_no' => $request->pi_inv_no[$i],
                    'awbno' => $request->awbno[$i],
                    'boeno' => $request->boeno[$i],
                    //'bill_no' => $request->table_id_stl_billno_.''.$request->purchase_inv[$i],
                ];
            }
            SysSTLItems::insert($temp_data);

            DB::commit();
            Toastr::success('STL Added Successful', 'Success');
            return redirect('stl/' . $stl->id);


        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function download($id)
    {
        try {
            $stl = SysSTL::find($id);
            $vendorDet = SysCustSuppl::where('code', $stl->vendor_name->account_code)->first();
            $stlItems = SysSTLItems::where('stl_id', $id)->get();
            if ($stl->pi_no == 1) {
                $tax_invoices = "Purchase Invoice";
            } elseif ($stl->pi_no == 2) {
                $tax_invoices = "Proforma Invoice";
            } else {
                $tax_invoices = "Purchase Order";
            }
            $invoices_no = $stlItems->pluck('pi_inv_no')->unique()->implode(', ');

            $with_with_out = $stl->with_amount;

            $company = SysCompany::find($stl->company_id);
            $print = date('d/m/Y h:i A', strtotime(Carbon::now('+04:00')));

            $data = [
                'company' => $company,
                'stl' => $stl,
                'stl_items' => $stlItems,
                'vendor_det' => $vendorDet,
                'print' => $print,
                'tax_invoices' => $tax_invoices,
                'invoices_no' => $invoices_no,
                'with_with_out' => $with_with_out,
            ];

            //return view('backEnd.pdf_print.stl_pdf', compact('company','stl','stlItems','vendorDet','print','tax_invoices','invoices_no'));
            $pdf = PDF::loadView('backEnd.pdf_print.stl_pdf', $data);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download($stl->doc_number . ".pdf");

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
            $company = SysCompany::where('id', session('logged_session_data.company_id'))->first();
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $currency_code = $currency->where('id', $company->currency_id)->first();
            $currency_code = $currency_code->code;
            $vendor = SysHelper::get_stl_supplier_list($company_id);
            $bank = SysHelper::get_stl_bank_account();
            $product = DB::table('sm_items as items')->select('items.id', 'items.part_number', 'items.description', 'cat.category_name as cat_name')
                ->join('sm_item_categories as cat', 'cat.id', 'items.category_name')->where('items.status', 1)->orderby('items.part_number', 'asc')->get();
            $edit = SysSTL::where('id', $id)->first();
            $edit_items = SysSTLItems::where('stl_id', $id)->get();

            return view('backEnd.stl.stl_view', compact('vendor', 'bank', 'product', 'company', 'currency', 'currency_code', 'edit', 'edit_items'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $stl = SysSTL::where('company_id', $company_id)->orderby('id', 'desc')->get();
            $company = SysCompany::where('id', session('logged_session_data.company_id'))->first();
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $currency_code = $currency->where('id', $company->currency_id)->first();
            $currency_code = $currency_code->code;
            $vendor = SysHelper::get_stl_supplier_list($company_id);
            $bank = SysHelper::get_stl_bank_account();
            $product = DB::table('sm_items as items')->select('items.id', 'items.part_number', 'items.description', 'cat.category_name as cat_name')
                ->join('sm_item_categories as cat', 'cat.id', 'items.category_name')->where('items.status', 1)->orderby('items.part_number', 'asc')->get();
            $edit = SysSTL::where('id', $id)->first();
            $edit_items = SysSTLItems::where('stl_id', $id)->get();

            return view('backEnd.stl.stl_edit', compact('vendor', 'bank', 'product', 'company', 'currency', 'currency_code', 'edit', 'edit_items', 'stl'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update(Request $request)
    {
        try {
    
            DB::beginTransaction();

            $stl = SysSTL::find($request->stl_id);
            $stl->doc_date = SysHelper::normalizeToYmd($request->doc_date);
            $stl->bank = $request->bank;
            $stl->exchange_rate = $request->exchange_rate;
            $stl->amount_usd = floatval(str_replace(',', '', $request->amount_usd));
            $stl->amount_aed = floatval(str_replace(',', '', $request->amount_aed));
            $stl->currency = $request->currency;
            $stl->currency_m = $request->currency_m;
            $stl->owner_name = $request->owner_name;
            $stl->bank_representative = $request->bank_representative;
            $stl->vendor = $request->vendor;
            $stl->payment_type = $request->payment_type;
            $stl->pi_no = $request->pi_no;
            $stl->submition_date = SysHelper::normalizeToYmd($request->submition_date);
            $stl->narration = $request->narration;
            $stl->partial_remarks = $request->partial_remarks;
            $stl->with_amount = $request->with_amount;
            $stl->status = 1;
            $stl->updated_by = Auth::user()->id;
            $stl->updated_at = Carbon::now('+04:00');
            $results = $stl->save();
            $stl->toArray();

            // for($i = 0; $i < count($request->partno); $i++) {
            //     if($request->pi_no==1){
            //     $pi_no = $request->purchase_inv[$i]; }
            //     else { $pi_no = 0; }

            //     if($request->partno[$i]==0){
            //         $partno= db::table('sm_items')->select('id')->where('part_number',$request->part_number[$i])->where('status',1)->first();
            //         if(isset($partno)){
            //             $partno=$partno->id;
            //         } else { $partno=0; }
            //     } else { $partno=$request->partno[$i]; }

            //     $temp_data[]=[
            //         'stl_id' => $stl->id,
            //         'pi_no' => $pi_no,
            //         'part_id' => $request->partno[$i],
            //         'part_no' => $request->part_number[$i],
            //         'description' => $request->description[$i],
            //         'amount' => floatval(str_replace(',', '', $request->amount[$i])),
            //         'status' => 1,
            //         'pi_inv_no' => $request->pi_inv_no[$i],
            //         'awbno' => $request->awbno[$i],
            //         'boeno' => $request->boeno[$i],
            //         //'bill_no' => $request->table_id_stl_billno_.''.$request->purchase_inv[$i],
            //     ];
            // }
            // SysSTLItems::insert($temp_data);

            DB::commit();
            Toastr::success('STL Updated Successful', 'Success');
            return redirect()->back();


        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function stl_add_item(Request $request)
    {
        try {
            $item = SmItem::where('id', $request->add_part_no)->first();
            SysSTLItems::insert([
                'stl_id' => $request->add_stl_id,
                'pi_no' => $request->add_pi_no,
                'part_id' => $request->add_part_no,
                'part_no' => $item->part_number,
                'description' => $item->description,
                'amount' => floatval(str_replace(',', '', $request->add_amount)),
                'status' => 1,
                'pi_inv_no' => $request->add_pi_inv_no,
            ]);

            Toastr::success('Item Added Successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    function stl_update_item(Request $request)
    {
        try {
            $item = SmItem::where('id', $request->edit_part_no)->first();
            DB::table('sys_stl_items')->where('id', $request->edit_item)->update([
                'part_id' => $request->edit_part_no,
                'part_no' => $item->part_number,
                'description' => $item->description,
                'amount' => floatval(str_replace(',', '', $request->edit_amount)),
                'status' => 1,
            ]);

            Toastr::success('Item Updated Successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    function delete_stl_items(Request $request)
    {
        try {
            SysSTLItems::where('id', $request->id)->delete();
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }


    public function report(Request $request)
    {
        try {
            $company_id = session('logged_session_data.company_id');
            $ctrl_bank = 0;
            $from_date = date('Y-m-01');
            $to_date = date('Y-m-d');
            $filter_by="";
            $bank = SysHelper::get_stl_bank_account();
            if (!is_countable($bank)) {
                $bank = collect();
            }
            if (count($bank) > 0) {
                if ($_POST) {
                    $ctrl_bank = $request->bank;


                    if ($request->from_date != "" && $request->filter_by == "") {
                    $from_date= SysHelper::normalizeToYmd($request->from_date);

                }
                if ($request->to_date != "" && $request->filter_by == "") {
                    $to_date=  SysHelper::normalizeToYmd($request->to_date); 
                }

                   
                if ($request->filter_by == "this_month") {
                    $from_date=date('Y-m-01');
                    $to_date=date("Y-m-t", strtotime($from_date));
                    $filter_by='this_month';               
                }
                if ($request->filter_by == "today") {
                    $from_date=date('Y-m-d');
                    $to_date=date('Y-m-d');
                    $filter_by='today';
                }
                if ($request->filter_by == "this_week") {
                    $from_date = date('Y-m-d', strtotime('-1 week sunday 00:00:00'));
                    $to_date = date('Y-m-d', strtotime('saturday 23:59:59'));
                    $filter_by='this_week';
                }
                if ($request->filter_by == "last_week") {
                    $from_date = date('Y-m-d', strtotime('-2 week sunday 00:00:00'));
                    $to_date = date('Y-m-d', strtotime('-1 week saturday 23:59:59'));
                    $filter_by='last_week';
                }
                if ($request->filter_by == "last_month") {
                    $from_date = date('Y-m-d', strtotime('first day of previous month'));
                    $to_date = date('Y-m-d', strtotime('last day of previous month'));
                    $filter_by='last_month';
                }
                if ($request->filter_by == "this_quarter") {
                    $q_date = SysHelper::get_quarter(date('m'));
                    $from_date = $q_date[0];
                    $to_date = $q_date[1];
                    $filter_by='this_quarter';
                }
                if ($request->filter_by == "pre_quarter") {
                    $q_date = SysHelper::get_pre_quarter(date('m'));
                    $from_date = $q_date[0];
                    $to_date = $q_date[1];
                    $filter_by='pre_quarter';
                }
                if ($request->filter_by == "this_year") {
                    $from_date = date('Y-01-01');
                    $to_date = date('Y-12-31');
                    $filter_by='this_year';
                }
                if ($request->filter_by == "last_year") {
                    $from_date = date("Y-01-01",strtotime("-1 year"));
                    $to_date = date("Y-12-31",strtotime("-1 year"));
                    $filter_by='last_year';
                }


                    

                } else {
                    $ctrl_bank = $bank[0]->id;
                }
            }

            $data1 = SysChartofAccounts::select('id', 'account_name', 'created_at', 'stl_limit', 'status')
                ->selectRaw('1 as type')
                ->where('company_id', $company_id)
                ->where('id', $ctrl_bank)
                ->where('stl', 1)
                ->orderby('id', 'desc')
                ->get();
            $data2 = SysSTL::select(
                'id',
                'doc_number',
                'doc_date',
                'amount_usd',
                'amount_aed',
                'vendor',
                'submition_date',
                'narration',
                'status',
                'stl_ref_no',
                'processing_date',
                'settlement_date',
                'stl_interest',
                'bank_charges',
                'other_charges'
            )
                ->selectRaw('2 as type')
                ->selectSub(function ($query) {
                    $query->selectRaw("GROUP_CONCAT(account_name) AS account_name") // Concatenating pi_inv_no with commas
                        ->from('sys_chartofaccounts')
                        ->whereColumn('sys_chartofaccounts.id', 'sys_stl.vendor'); // Join condition
                }, 'vendor_account_name')
                ->selectSub(function ($query) {
                    $query->selectRaw("GROUP_CONCAT(DISTINCT pi_inv_no) AS pi_inv_no") // Concatenating pi_inv_no with commas
                        ->from('sys_stl_items')
                        ->whereColumn('sys_stl_items.stl_id', 'sys_stl.id'); // Join condition
                }, 'pi_inv_no')
                ->selectSub(function ($query) {
                    $query->selectRaw("GROUP_CONCAT(DISTINCT awbno) AS awbno") // Concatenating bill_no with commas
                        ->from('sys_stl_items')
                        ->whereColumn('sys_stl_items.stl_id', 'sys_stl.id'); // Join condition
                }, 'awbno')
                ->where('company_id', $company_id)
                ->where('bank', $ctrl_bank)
                ->whereBetween('created_at', [$from_date, $to_date])
                ->orderby('id', 'desc')
                ->get();

            $data3 = SysSTLPayment::select('id', 'stl_id', 'payment_req_date', 'payment_stl_no', 'payment_stl_ref_no', 'payment_supplier_id', 'payment_supplier_name', 'payment_set_amount', 'payment_settlement_date', 'status')
                ->selectRaw('3 as type')
                ->where('company_id', $company_id)
                ->wherein('stl_id', $data2->pluck('id'))
                ->orderby('id', 'desc')
                ->get();
            $sortedData = array_merge_recursive($data1->toArray(), $data2->toArray(), $data3->toArray());

            return view('backEnd.stl.stl_report_list', compact('sortedData', 'bank', 'ctrl_bank', 'from_date', 'to_date','filter_by'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function supplier_report(Request $request)
    {
        try {
            $company_id = session('logged_session_data.company_id');
            $ctrl_vendor = 0;
            $vendor = SysHelper::get_stl_supplier_list($company_id);
            if (count($vendor) > 0) {
                if ($_POST) {
                    $ctrl_vendor = $request->vendor;
                } else {
                    $ctrl_vendor = $vendor[0]->id;
                }
            }

            $data1 = SysChartofAccounts::select('sys_chartofaccounts.id', 'sys_chartofaccounts.account_name', 'sys_chartofaccounts.created_at', 'sup.stl_limit', 'sup.stl_opb', 'sys_chartofaccounts.status')
                ->selectRaw('1 as type')
                ->join('sys_cust_suppl as sup', 'sup.code', 'sys_chartofaccounts.account_code')
                ->where('sys_chartofaccounts.company_id', $company_id)
                ->where('sys_chartofaccounts.id', $ctrl_vendor)
                ->orderby('sys_chartofaccounts.id', 'desc')
                ->get();
            $data2 = SysSTL::select(
                'id',
                'doc_number',
                'doc_date',
                'amount_usd',
                'amount_aed',
                'vendor',
                'submition_date',
                'narration',
                'status',
                'stl_ref_no',
                'processing_date',
                'settlement_date',
                'stl_interest',
                'bank_charges',
                'other_charges'
            )
                ->selectRaw('2 as type')
                ->selectSub(function ($query) {
                    $query->selectRaw("GROUP_CONCAT(account_name) AS account_name") // Concatenating pi_inv_no with commas
                        ->from('sys_chartofaccounts')
                        ->whereColumn('sys_chartofaccounts.id', 'sys_stl.vendor'); // Join condition
                }, 'vendor_account_name')
                ->selectSub(function ($query) {
                    $query->selectRaw("GROUP_CONCAT(DISTINCT pi_inv_no) AS pi_inv_no") // Concatenating pi_inv_no with commas
                        ->from('sys_stl_items')
                        ->whereColumn('sys_stl_items.stl_id', 'sys_stl.id'); // Join condition
                }, 'pi_inv_no')
                ->selectSub(function ($query) {
                    $query->selectRaw("GROUP_CONCAT(DISTINCT awbno) AS awbno") // Concatenating bill_no with commas
                        ->from('sys_stl_items')
                        ->whereColumn('sys_stl_items.stl_id', 'sys_stl.id'); // Join condition
                }, 'awbno')
                ->where('company_id', $company_id)
                ->where('vendor', $ctrl_vendor)
                ->orderby('id', 'desc')
                ->get();

            $data3 = SysSTLPayment::select('id', 'stl_id', 'payment_req_date', 'payment_stl_no', 'payment_stl_ref_no', 'payment_supplier_id', 'payment_supplier_name', 'payment_set_amount', 'payment_settlement_date', 'status')
                ->selectRaw('3 as type')
                ->where('company_id', $company_id)
                ->wherein('stl_id', $data2->pluck('id'))
                ->orderby('id', 'desc')
                ->get();
            $sortedData = array_merge_recursive($data1->toArray(), $data2->toArray(), $data3->toArray());

            return view('backEnd.stl.stl_report_supplier_list', compact('sortedData', 'vendor', 'ctrl_vendor'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function delete($id)
    {
        try {
            $company_id = session('logged_session_data.company_id');
            SysSTL::where('id', $id)->where('company_id', $company_id)->update(['status' => 2, 'updated_by' => Auth::user()->id, 'updated_at' => Carbon::now('+04:00')]);
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
            $company_id = session('logged_session_data.company_id');
            SysSTL::where('id', $id)->where('company_id', $company_id)->update(['status' => 1, 'updated_by' => Auth::user()->id, 'updated_at' => Carbon::now('+04:00')]);
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function edit_update(Request $request)
    {
        try {
            DB::beginTransaction();

            $stl = SysSTL::find($request->edit_stl_id);
            $stl->stl_ref_no = $request->stl_ref_no;
            $stl->processing_date = Carbon::createFromFormat('d/m/Y', $request->processing_date)->format('Y-m-d');
            $stl->settlement_date = Carbon::createFromFormat('d/m/Y', $request->settlement_date)->format('Y-m-d');
            $stl->stl_interest = $request->stl_interest;
            $stl->bank_charges = $request->bank_charges;
            $stl->other_charges = $request->other_charges;
            $stl->updated_by = Auth::user()->id;
            $stl->updated_at = Carbon::now('+04:00');
            $results = $stl->save();
            DB::commit();
            Toastr::success('STL Updated Successfully', 'Success');
            return redirect()->back();


        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function payment_add(Request $request)
    {
        try {
            DB::beginTransaction();

            $stl = new SysSTLPayment();
            $stl->stl_id = $request->payment_stl_id;
            $stl->payment_req_date = Carbon::createFromFormat('d/m/Y', $request->payment_req_date)->format('Y-m-d');
            $stl->payment_stl_no = $request->payment_stl_no;
            $stl->payment_stl_ref_no = $request->payment_stl_ref_no;
            $stl->payment_supplier_id = $request->payment_supplier_id;
            $stl->payment_supplier_name = $request->payment_supplier_name;
            $stl->payment_set_amount = $request->payment_set_amount;
            $stl->payment_settlement_date =  Carbon::createFromFormat('d/m/Y', $request->payment_settlement_date)->format('Y-m-d');
            $stl->status = 1;
            $stl->created_by = Auth::user()->id;
            $stl->created_at = Carbon::now('+04:00');
            $stl->company_id = session('logged_session_data.company_id');
            $results = $stl->save();
            DB::commit();
            Toastr::success('STL Payment Added Successfully', 'Success');
            return redirect()->back();


        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function payment_update(Request $request)
    {
        try {
            DB::beginTransaction();

            $stl = SysSTLPayment::find($request->payment_stl_id);
            $stl->payment_req_date = Carbon::createFromFormat('d/m/Y', $request->payment_req_date)->format('Y-m-d');
            $stl->payment_stl_ref_no = $request->payment_stl_ref_no;
            $stl->payment_supplier_id = $request->payment_supplier_id;
            $stl->payment_supplier_name = $request->payment_supplier_name;
            $stl->payment_set_amount = $request->payment_set_amount;
            $stl->payment_settlement_date =  Carbon::createFromFormat('d/m/Y', $request->payment_settlement_date)->format('Y-m-d');
            $stl->status = 1;
            $stl->updated_by = Auth::user()->id;
            $stl->updated_at = Carbon::now('+04:00');
            $results = $stl->save();
            DB::commit();
            Toastr::success('STL Payment Updated Successfully', 'Success');
            return redirect()->back();


        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }











    public function getpybalancelist(Request $request)
    {
        $company_id = session('logged_session_data.company_id');
        $opb = SysChartofAccountsTransaction::wherein('transaction_type', ['openingbalance11111', 'opbinvoice'])->where('account_id', $request->account_id)->where('status', 1)->where('company_id', $company_id)->get();
        $items = DB::select("CALL get_bank_payment_adjestments($request->account_id,$company_id)");

        //$siv_charges = SysSalesInvoiceCFCharges::where('');

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
                $paid = SysPaymentAdjustments::where('bi_doc_no', $dt->transaction_no)->sum('bi_paid');
                $searchData[] = [
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
                'doc_number' => $item->doc_number,
                'doc_date' => $item->doc_date,
                'lpo_number' => $item->lpo_number,
                'lpo_date' => $item->lpo_date,
                'total' => $item->total,
                'paid' => $item->paid,
                'balance' => $item->balance,
            ];
        }

        if (!empty($searchData)) {
            return json_encode($searchData);
        }
    }
    public function getpybalancelistedit(Request $request)
    {
        $company_id = session('logged_session_data.company_id');
        $opb = SysChartofAccountsTransaction::wherein('transaction_type', ['openingbalance11111', 'opbinvoice'])->where('account_id', $request->account_id)->where('status', 1)->where('company_id', $company_id)->get();
        $items = DB::select("CALL get_bank_payment_adjestments_edit($request->account_id,$company_id)");

        $searchData = [];

        $adjestData = SysPaymentAdjustments::where('bi_doc_number', $request->doc_number)->get();

        if (count($opb) > 0) {
            foreach ($opb as $dt) {
                $paid = SysPaymentAdjustments::where('bi_doc_no', $dt->transaction_no)->sum('bi_paid');
                $bi_amount = $adjestData->where('bi_doc_no', $dt->transaction_no)->sum('bi_paid');
                if ($bi_amount != 0) {
                    $paid = 0;
                }
                $searchData[] = [
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
            $searchData[] = [
                'doc_number' => $item->doc_number,
                'doc_date' => $item->doc_date,
                'lpo_number' => $item->lpo_number,
                'lpo_date' => $item->lpo_date,
                'total' => $item->total,
                'paid' => $item->paid,
                'bi_amount' => $bi_amount,
                'balance' => $item->balance,
            ];
        }

        if (!empty($searchData)) {
            return json_encode($searchData);
        }
    }



    public function delete_payment_items(Request $request)
    {
        try {
            db::beginTransaction();
            SysChartofAccountsTransaction::where(['id' => $request->id])->delete();
            SysPaymentAdjustments::where(['account_id' => $request->account_id])->where('bi_doc_number', $request->transaction_no)->delete();

            $amount = SysChartofAccountsTransaction::where(['transaction_no' => $request->transaction_no])->sum('debit_amount');
            SysChartofAccountsTransaction::where(['transaction_no' => $request->transaction_no])
                ->where(['is_main_account' => 1])->where(['debit_amount' => '0.00'])->update(['credit_amount' => $amount]);

            db::commit();

            $ret = 'SUCCESS';
            return json_encode(array('data' => $ret));

        } catch (\Exception $e) {
            db::rollBack();
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    public function payment_cheque_list(Request $request)
    {
        try {
            $com_id = session('logged_session_data.company_id');
            $bank = SysChartofAccounts::select('id', 'account_name', 'account_code')->where('subgroup2', 6)->where('company_id', $com_id)->orderby('id', 'asc')->get();
            $supplier = SysChartofAccounts::select('id', 'account_name', 'account_code')->where('subgroup2', 19)->where('company_id', $com_id)->orderby('account_name', 'asc')->get();
            $data = SysPaymentCheque::where('company_id', $com_id)->get();

            $curr = SysHelper::cheque_print_currancy_code($com_id);
            $currency1 = $curr[0];
            $currency2 = $curr[1];

            return view('backEnd.payment.payment_cheque_list', compact('data', 'bank', 'supplier', 'currency1', 'currency2'));
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function payment_cheque_store(Request $request)
    {
        try {
            db::beginTransaction();
            $attachment = "";
            if ($request->file('attachment') != "") {
                $file = $request->file('attachment');
                $attachment = md5(time()) . "." . $file->getclientoriginalextension();
                $file->move('public/uploads/payment_cheque/', $attachment);
                $attachment = $attachment;
            }

            $isdone = DB::table('sys_payment_cheque')->where([
                'bank_name' => $request->bank_name,
                'cheque_number' => $request->cheque_number,
                'cheque_date' => $request->cheque_date,
                'supplier_name' => $request->supplier_name,
            ])->get();
            if (count($isdone) == 0) {
                $id = DB::table('sys_payment_cheque')->insertGetId(
                    [
                        'doc_number' => SysHelper::get_new_code('sys_payment_cheque', 'CH', 'doc_number'),
                        'doc_date' => Carbon::now('+04:00'),
                        'bank_name' => $request->bank_name,
                        'cheque_number' => $request->cheque_number,
                        'cheque_date' => $request->cheque_date,
                        'supplier_name' => $request->supplier_name,
                        'other_supplier_name' => $request->other_supplier_name,
                        'amount' => str_replace(',', '', $request->amount),
                        'amount_words' => $request->amount_words,
                        'deal_id' => SysHelper::get_dealid_from_code($request->deal_id),
                        'attachment' => $attachment,
                        'reference' => $request->reference,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                        'company_id' => session('logged_session_data.company_id'),
                    ]
                );
            } else {
                DB::table('sys_payment_cheque')->where('id', $isdone[0]->id)->update(
                    [
                        'bank_name' => $request->bank_name,
                        'cheque_number' => $request->cheque_number,
                        'cheque_date' => $request->cheque_date,
                        'supplier_name' => $request->supplier_name,
                        'other_supplier_name' => $request->other_supplier_name,
                        'amount' => str_replace(',', '', $request->amount),
                        'amount_words' => $request->amount_words,
                        'deal_id' => SysHelper::get_dealid_from_code($request->deal_id),
                        'attachment' => $attachment,
                        'reference' => $request->reference,
                        'status' => 1,
                        'updated_by' => Auth::user()->id,
                        'updated_at' => Carbon::now('+04:00'),
                    ]
                );
            }

            db::commit();

            if ($request->submit_btn == "pr") {

                if (count($isdone) == 0) {
                    $pdata = SysPaymentCheque::where('id', $id)->first();
                } else {
                    $pdata = SysPaymentCheque::where('id', $isdone[0]->id)->first();
                }

                if ($pdata->supplier_name == 0) {
                    $supplier_name = $pdata->other_supplier_name;
                } else {
                    $supplier_name = $pdata->suppliername->account_name;
                }
                $company_top = '285px';
                $company_left = '425px';
                $date_top = '220px';
                $date_left = '836px';
                $amount_w_top = '316px';
                $amount_w_left = '326px';
                $amount_top = '355px';
                $amount_left = '834px';
                $font_size = '13px';
                $temp_data = DB::table('sys_payment_cheque_template')->where('bank_id', $pdata->bank_name)->orderby('id', 'desc')->first();
                if (isset($temp_data)) {
                    $company_top = $temp_data->company_top;
                    $company_left = $temp_data->company_left;
                    $date_top = $temp_data->date_top;
                    $date_left = $temp_data->date_left;
                    $amount_w_top = $temp_data->amount_w_top;
                    $amount_w_left = $temp_data->amount_w_left;
                    $amount_top = $temp_data->amount_top;
                    $amount_left = $temp_data->amount_left;
                    $font_size = $temp_data->font_size;
                }
                $data = [
                    'cheque_date' => $pdata->cheque_date,
                    'supplier_name' => $supplier_name,
                    'amount' => $pdata->amount,
                    'amount_words' => $pdata->amount_words,
                    'company_top' => $company_top,
                    'company_left' => $company_left,
                    'date_top' => $date_top,
                    'date_left' => $date_left,
                    'amount_w_top' => $amount_w_top,
                    'amount_w_left' => $amount_w_left,
                    'amount_top' => $amount_top,
                    'amount_left' => $amount_left,
                    'font_size' => $font_size,
                ];
                $pdf = PDF::loadView('backEnd.pdf_print.cheque_pdf', $data);
                $paper_size = array(0, 0, 750, 500);
                $pdf->setPaper($paper_size);
                //$pdf->setPaper('A4', 'portrait');
                //return redirect()->back()->$pdf->download('cheque-print.pdf');
                return $pdf->download("cheque-print.pdf");
            }

            if ($request->submit_btn == "jv") {
                return redirect('journalvoucheradd/' . $request->cheque_date);
            }
            if ($request->submit_btn == "py") {
                return redirect('payment-add-from-cheque/' . $id);
            }

            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            db::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function payment_cheque_update(Request $request)
    {
        try {
            db::beginTransaction();
            $attachment = $request->edit_attachment;
            if ($request->file('attachment') != "") {
                $file = $request->file('attachment');
                $attachment = md5(time()) . "." . $file->getclientoriginalextension();
                $file->move('public/uploads/payment_cheque/', $attachment);
                $attachment = $attachment;
            }

            DB::table('sys_payment_cheque')->where('id', $request->cid)->update(
                [
                    'bank_name' => $request->bank_name,
                    'cheque_number' => $request->cheque_number,
                    'cheque_date' => $request->cheque_date,
                    'supplier_name' => $request->supplier_name,
                    'other_supplier_name' => $request->other_supplier_name,
                    'amount' => str_replace(',', '', $request->amount),
                    'amount_words' => $request->amount_words,
                    'deal_id' => SysHelper::get_dealid_from_code($request->deal_id),
                    'attachment' => $attachment,
                    'reference' => $request->reference,
                    'status' => 1,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]
            );

            db::commit();

            if ($request->submit_btn == "pr") {
                $pdata = SysPaymentCheque::where('id', $request->cid)->first();
                if ($pdata->supplier_name == 0) {
                    $supplier_name = $pdata->other_supplier_name;
                } else {
                    $supplier_name = $pdata->suppliername->account_name;
                }
                $company_top = '285px';
                $company_left = '425px';
                $date_top = '220px';
                $date_left = '836px';
                $amount_w_top = '316px';
                $amount_w_left = '326px';
                $amount_top = '355px';
                $amount_left = '834px';
                $font_size = '13px';
                $temp_data = DB::table('sys_payment_cheque_template')->where('bank_id', $pdata->bank_name)->orderby('id', 'desc')->first();
                if (isset($temp_data)) {
                    $company_top = $temp_data->company_top;
                    $company_left = $temp_data->company_left;
                    $date_top = $temp_data->date_top;
                    $date_left = $temp_data->date_left;
                    $amount_w_top = $temp_data->amount_w_top;
                    $amount_w_left = $temp_data->amount_w_left;
                    $amount_top = $temp_data->amount_top;
                    $amount_left = $temp_data->amount_left;
                    $font_size = $temp_data->font_size;
                }
                $data = [
                    'cheque_date' => $pdata->cheque_date,
                    'supplier_name' => $supplier_name,
                    'amount' => $pdata->amount,
                    'amount_words' => $pdata->amount_words,
                    'company_top' => $company_top,
                    'company_left' => $company_left,
                    'date_top' => $date_top,
                    'date_left' => $date_left,
                    'amount_w_top' => $amount_w_top,
                    'amount_w_left' => $amount_w_left,
                    'amount_top' => $amount_top,
                    'amount_left' => $amount_left,
                    'font_size' => $font_size,
                ];
                $pdf = PDF::loadView('backEnd.pdf_print.cheque_pdf', $data);
                $paper_size = array(0, 0, 750, 500);
                $pdf->setPaper($paper_size);
                //$pdf->setPaper('A4', 'portrait');
                return $pdf->download("cheque-print.pdf");
            }
            if ($request->submit_btn == "jv") {
                return redirect('journalvoucheradd/' . $request->cheque_date);
            }
            if ($request->submit_btn == "py") {
                return redirect('payment-add-from-cheque/' . $request->cid);
            }

            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            db::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function payment_cheque_print($id)
    {
        try {

            $pdata = SysPaymentCheque::where('id', $id)->first();
            $company_top = '285px';
            $company_left = '425px';
            $date_top = '220px';
            $date_left = '836px';
            $amount_w_top = '316px';
            $amount_w_left = '326px';
            $amount_top = '355px';
            $amount_left = '834px';
            $font_size = '13px';
            $temp_data = DB::table('sys_payment_cheque_template')->where('bank_id', $pdata->bank_name)->orderby('id', 'desc')->first();
            if (isset($temp_data)) {
                $company_top = $temp_data->company_top;
                $company_left = $temp_data->company_left;
                $date_top = $temp_data->date_top;
                $date_left = $temp_data->date_left;
                $amount_w_top = $temp_data->amount_w_top;
                $amount_w_left = $temp_data->amount_w_left;
                $amount_top = $temp_data->amount_top;
                $amount_left = $temp_data->amount_left;
                $font_size = $temp_data->font_size;
            }

            if ($pdata->supplier_name == 0) {
                $supplier_name = $pdata->other_supplier_name;
            } else {
                $supplier_name = $pdata->suppliername->account_name;
            }
            $data = [
                'cheque_date' => $pdata->cheque_date,
                'supplier_name' => $supplier_name,
                'amount' => $pdata->amount,
                'amount_words' => $pdata->amount_words,
                'company_top' => $company_top,
                'company_left' => $company_left,
                'date_top' => $date_top,
                'date_left' => $date_left,
                'amount_w_top' => $amount_w_top,
                'amount_w_left' => $amount_w_left,
                'amount_top' => $amount_top,
                'amount_left' => $amount_left,
                'font_size' => $font_size,
            ];
            $pdf = PDF::loadView('backEnd.pdf_print.cheque_pdf', $data);
            $paper_size = array(0, 0, 750, 500);
            $pdf->setPaper($paper_size);
            //$pdf->setPaper('A4', 'portrait');
            return $pdf->download("cheque-print.pdf");
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public function payment_cheque_print_template(Request $request)
    {
        try {
            $bankid = 0;
            $bank = SysChartofAccounts::select('id', 'account_name', 'account_code')->where('subgroup2', 6)->where('company_id', session('logged_session_data.company_id'))->orderby('id', 'asc')->get();
            if ($_POST) {
                $bankid = $request->bank_id;
                if ($request->btn_submit == "save") {
                    $check = DB::table('sys_payment_cheque_template')->where('bank_id', $request->bank_id)->orderby('id', 'desc')->first();
                    if (isset($check)) {
                        DB::table('sys_payment_cheque_template')->where('bank_id', $request->bank_id)->update([
                            'company_top' => $request->company_top,
                            'company_left' => $request->company_left,
                            'date_top' => $request->date_top,
                            'date_left' => $request->date_left,
                            'amount_w_top' => $request->amount_w_top,
                            'amount_w_left' => $request->amount_w_left,
                            'amount_top' => $request->amount_top,
                            'amount_left' => $request->amount_left,
                            'font_size' => $request->font_size,
                            'status' => $request->status,
                            'updated_by' => Auth::user()->id,
                            'updated_at' => Carbon::now('+04:00'),
                            'company_id' => session('logged_session_data.company_id'),
                        ]);
                    } else {
                        DB::table('sys_payment_cheque_template')->insert([
                            'bank_id' => $request->bank_id,
                            'company_top' => $request->company_top,
                            'company_left' => $request->company_left,
                            'date_top' => $request->date_top,
                            'date_left' => $request->date_left,
                            'amount_w_top' => $request->amount_w_top,
                            'amount_w_left' => $request->amount_w_left,
                            'amount_top' => $request->amount_top,
                            'amount_left' => $request->amount_left,
                            'font_size' => $request->font_size,
                            'status' => $request->status,
                            'created_by' => Auth::user()->id,
                            'created_at' => Carbon::now('+04:00'),
                            'company_id' => session('logged_session_data.company_id'),
                        ]);
                    }
                    Toastr::success('Updated Successfully', 'Success');
                }
            }
            $company_top = '285px';
            $company_left = '425px';
            $date_top = '220px';
            $date_left = '836px';
            $amount_w_top = '316px';
            $amount_w_left = '326px';
            $amount_top = '355px';
            $amount_left = '834px';
            $font_size = '13px';
            if ($_POST) {
                $temp_data = DB::table('sys_payment_cheque_template')->where('bank_id', $request->bank_id)->orderby('id', 'desc')->first();
            } else {
                $temp_data = DB::table('sys_payment_cheque_template')->where('bank_id', $bank[0]->id)->orderby('id', 'desc')->first();
            }
            if (isset($temp_data)) {
                $company_top = $temp_data->company_top;
                $company_left = $temp_data->company_left;
                $date_top = $temp_data->date_top;
                $date_left = $temp_data->date_left;
                $amount_w_top = $temp_data->amount_w_top;
                $amount_w_left = $temp_data->amount_w_left;
                $amount_top = $temp_data->amount_top;
                $amount_left = $temp_data->amount_left;
                $font_size = $temp_data->font_size;
            }
            $company = SysCompany::select('company_name')->where('id', session('logged_session_data.company_id'))->first();
            $company = $company->company_name;
            $cheque_date = "15/01/2025";
            $cheque_amount_w = "Sixty Thousand Eight Hundred Sixty Thousand Eight Hundred Only";
            $cheque_amount = "60,800.00";

            return view('backEnd.payment.payment_cheque_template', compact('temp_data', 'company', 'cheque_date', 'cheque_amount', 'cheque_amount_w', 'company_top', 'company_left', 'date_top', 'date_left', 'amount_w_top', 'amount_w_left', 'amount_top', 'amount_left', 'font_size', 'bank', 'bankid'));

        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function payment_cheque_delete($id)
    {
        try {
            db::beginTransaction();
            DB::table('sys_payment_cheque')->where('id', $id)->update(
                [
                    'status' => 0,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]
            );
            db::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            db::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function payment_cheque_restore($id)
    {
        try {
            db::beginTransaction();
            DB::table('sys_payment_cheque')->where('id', $id)->update(
                [
                    'status' => 1,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]
            );
            db::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            db::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
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
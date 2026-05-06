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

use App\SysClearance;
use App\SysClearanceItems;

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
use App\SysCrmDeals;
use App\SysCrmDealTrackApprovalInvoice;
use App\SysCrmQuoteItems;
use App\SysHelper;
use App\SysItemStock;
use App\SysLedgerEntries;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

use function GuzzleHttp\Promise\exception_for;


class SysClearanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id = null)
    {
        try {

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $customer_list = SysHelper::get_customer_list($company_id);

            $query = SysClearance::wherein('company_id', $company_id);

            $ctrl_document_number = null;
            $ctrl_bill_to = null;
            $ctrl_ship_to = null;
            $ctrl_deal_number = null;
            $ctrl_invoice_no = null;
            $ctrl_invoice_date = null;

            if (SysHelper::get_pagination_post($request)) {
                if ($request->documents_number != "") {
                    $query->where('doc_no', 'like', '%' . $request->documents_number . '%');
                    $ctrl_document_number = $request->documents_number;

                }
                if ($request->bill_to != "") {
                    $query->where('bill_to', 'like', '%' . $request->bill_to . '%');
                    $ctrl_bill_to = $request->bill_to;
                }
                if ($request->ship_to != "") {
                    $query->where('ship_to', 'like', '%' . $request->ship_to . '%');
                    $ctrl_ship_to = $request->ship_to;

                }
                if ($request->deal_number != "") {
                    $query->where('deal_id', 'like', '%' . SysHelper::get_dealid_from_code($request->deal_number) . '%');
                    $ctrl_deal_number = $request->deal_number;

                }
                if ($request->invoice_no != "") {
                    $query->where('invoice_no', 'like', '%' . $request->invoice_no . '%');
                    $ctrl_invoice_no = $request->invoice_no;

                }
                if ($request->invoice_date != "") {
                    $ctrl_invoice_date = $request->invoice_date;
                    $request->invoice_date = SysHelper::normalizeToYmd($request->invoice_date);
                    $query->where('invoice_date', $request->invoice_date);

                }
            } else {

            }

            $clearance = $query->orderby('invoice_no', 'desc')->paginate(50);


            $active_id = $id;
            $selectedCLR = [];


            $action = false;
            $editData = [];
            $addData = [];


            if ($request->has('clr_action')) {
                $clrAction = $request->input('clr_action');

                if ($clrAction === 'add') {
                    $action = 'add';
                    $addData = $this->create();

                } elseif ($clrAction === 'edit') {
                    $action = 'edit';
                    $editData = $this->edit($active_id); // Get all data for editing




                }
            } else {
                if ($id) {
                    $selectedCLR = $this->get_print_data($id);
                } else {
                    $firstRecord = $clearance->first();
                    if ($firstRecord) {
                        $active_id = $firstRecord->id;
                        $selectedCLR = $this->get_print_data($firstRecord->id);
                    }
                }
            }

            return view('backEnd/clearance/clearance_items_list', compact(
                'clearance',
                'action',
                'selectedCLR',
                'ctrl_document_number',
                'ctrl_bill_to',
                'ctrl_ship_to',
                'ctrl_deal_number',
                'ctrl_invoice_no',
                'ctrl_invoice_date',
                'active_id',
                'addData',
                'editData'
            ));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            //return redirect()->back();
            return $e;
        }
    }

    public function get_print_data($id)
    {
        try {
            $cl = SysClearance::find($id);

            if (!empty($cl)) {
                $cl_item = SysClearanceItems::where('clearance_id', '=', $cl->id)->get();

                $total = DB::table('sys_clearance_items')
                    ->select(DB::raw('SUM(weight) AS weight1'), DB::raw('SUM(qty) AS qty1'), DB::raw('SUM(price) AS price1'), DB::raw('SUM(totalprice) AS totalprice1'))
                    ->where('clearance_id', '=', $cl->id)->get();

                $coos = DB::table('sys_clearance_items')->select(DB::raw('DISTINCT coo'))->where('clearance_id', '=', $cl->id)->get();
                $currency = DB::table('sys_currency')->select('code')->where('id', '=', $cl->currency)->first();

                $data = [
                    'cl' => $cl,
                    'cl_item' => $cl_item,
                    'total' => $total,
                    'coos' => $coos,
                    'currency' => $currency,
                ];

                return $data;
                //$pdf = PDF::loadView('backEnd.clearance.clearance_pdf', $data);
                //$pdf->setPaper('A4', 'portrait');
                //return $pdf->download("Clearance_".$cl->invoice_no.".pdf");
            } else {
                return "error!!";
                //return view('web.syscom_credit_application_form');
            }
        } catch (\Throwable $th) {
            return $th;
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
            $currency = SysCurrencySettings::all();
            // $items = SysHelper::get_product_list($company_id);

            // $cart = DB::table('sys_clearance_items_cart')->where('cart_id', session('logged_session_data.cart_id'))->delete();

            // $cart = DB::table('sys_clearance_items_cart')->where('cart_id', session('logged_session_data.cart_id'))->get();

            $invoice_no = null;
            $deal_id = null;
            return compact('currency', 'invoice_no', 'deal_id');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            //return redirect()->back(); 
            return $e;
        }
    }
    public function create2($invoice_no = null, $account_id = null, $deal_id = null)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $currency = SysCurrencySettings::all();
            $items = SysHelper::get_product_list($company_id);

            $customer_address = SysCrmDeals::where('id', $deal_id)->first();
            //return $invoice_no;

            $cart = DB::table('sys_clearance_items_cart')->where('cart_id', session('logged_session_data.cart_id'))->get();

            if (count($cart) == 0) {
                return redirect('clearance/create');
            }
            return view('backEnd/clearance/add_clearance', compact('items', 'currency', 'cart', 'customer_address', 'invoice_no', 'deal_id'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            //return redirect()->back(); 
            return $e;
        }
    }

    function add_clearance_items_cart(Request $request)
    {
        try {
            $check_cart = DB::table('sys_clearance_items_cart')->where([
                'cart_id' => session('logged_session_data.cart_id'),
                'pid' => $request->pid,
            ])->count();
            if ($check_cart == 0) {
                DB::table('sys_clearance_items_cart')->insert([
                    'cart_id' => session('logged_session_data.cart_id'),
                    'pid' => $request->pid,
                    'partno' => $request->partno,
                    'description' => $request->description,
                    'coo' => $request->coo,
                    'hscode' => $request->hscode,
                    'weight' => $request->weight,
                    'qty' => $request->qty,
                    'price' => $request->price,
                    'totalprice' => $request->totalprice,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]);
            }
            $ret = DB::table('sys_clearance_items_cart')->where('cart_id', session('logged_session_data.cart_id'))->get();
            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = "ERROR";
            return json_encode(array('data' => $ret));
        }
    }
    function update_clearance_items_cart(Request $request)
    {
        try {
            DB::table('sys_clearance_items_cart')->where('id', $request->itm_id)->update([
                'cart_id' => session('logged_session_data.cart_id'),
                'pid' => $request->pid,
                'partno' => $request->partno,
                'description' => $request->description,
                'coo' => $request->coo,
                'hscode' => $request->hscode,
                'weight' => $request->weight,
                'qty' => $request->qty,
                'price' => $request->price,
                'totalprice' => $request->totalprice,
                'status' => 1,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now('+04:00'),
            ]);
            $ret = DB::table('sys_clearance_items_cart')->where('cart_id', session('logged_session_data.cart_id'))->get();
            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = $request->all();
            return json_encode(array('data' => $ret));
        }
    }
    function delete_clearance_items_cart(Request $request)
    {
        try {
            DB::table('sys_clearance_items_cart')->where('id', $request->id)->delete();
            $ret = DB::table('sys_clearance_items_cart')->where('cart_id', session('logged_session_data.cart_id'))->get();
            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = $request->all();
            return json_encode(array('data' => $ret));
        }
    }

    function get_clearance_items_list(Request $request)
    {
        try {
            $ret = DB::table('sm_items')->select('id', 'part_number', 'description', 'coo', 'hscode', 'weight')->where('id', $request->pid)->get();
            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = $request->all();
            return json_encode(array('data' => $ret));
        }
    }

    function add_clearance_items(Request $request)
    {
        try {
            $check_cart = DB::table('sys_clearance_items')->where([
                'clearance_id' => $request->clearance_id,
                'pid' => $request->pid,
            ])->count();
            if ($check_cart == 0) {
                DB::table('sys_clearance_items')->insert([
                    'clearance_id' => $request->clearance_id,
                    'pid' => $request->pid,
                    'partno' => $request->partno,
                    'description' => $request->description,
                    'coo' => $request->coo,
                    'hscode' => $request->hscode,
                    'weight' => $request->weight,
                    'qty' => $request->qty,
                    'price' => $request->price,
                    'totalprice' => $request->totalprice,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]);
            }
            $ret = DB::table('sys_clearance_items')->where('clearance_id', $request->clearance_id)->get();
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
    function update_clearance_items(Request $request)
    {
        try {
            DB::table('sys_clearance_items')->where('id', $request->itm_id)->update([
                'clearance_id' => $request->clearance_id,
                'pid' => $request->pid,
                'partno' => $request->partno,
                'description' => $request->description,
                'coo' => $request->coo,
                'hscode' => $request->hscode,
                'weight' => $request->weight,
                'qty' => $request->qty,
                'price' => $request->price,
                'totalprice' => $request->totalprice,
                'status' => 1,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now('+04:00'),
            ]);
            $ret = DB::table('sys_clearance_items')->where('clearance_id', $request->clearance_id)->get();
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
    function delete_clearance_items(Request $request)
    {
        try {
            DB::table('sys_clearance_items')->where('id', $request->id)->delete();
            $ret = DB::table('sys_clearance_items')->where('clearance_id', $request->clearance_id)->get();
            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = $request->all();
            return json_encode(array('data' => $ret));
        }
    }

    public function store(Request $request)
    {

        // $cart = DB::table('sys_clearance_items_cart')->where('cart_id', session('logged_session_data.cart_id'))->get();
        // if (count($cart) == 0) {
        //     Toastr::error('Items not found', 'Failed');
        //     return redirect()->back();
        // }


        DB::beginTransaction();
        try {
            $cl = new SysClearance();
            $cl->doc_no = SysHelper::get_new_code_normal('sys_clearance', 'SYZ', 'invoice_no');
            $cl->invoice_no = $request->invoice_no;
            $cl->invoice_date = SysHelper::normalizeToYmd($request->invoice_date);
            $cl->currency = $request->currency;
            $cl->free_zone_bill_no = $request->free_zone_bill_no;
            $cl->goods_description = $request->goods_description;
            $cl->bill_to = $request->bill_to;
            $cl->bill_to_address = "";
            $cl->ship_to = $request->ship_to;
            $cl->ship_to_address = $request->ship_to_address;
            $cl->boe_no = $request->boe_no;
            $cl->payment_method = implode(', ', $request->payment_method);
            $cl->customer_bill_type = implode(', ', $request->customer_bill_type);

            $cl->box_type = $request->box_type;
            $cl->box_qty = $request->box_qty;
            $cl->cbm = $request->cbm;
            $cl->exit_point = $request->exit_point;
            $cl->destination = $request->destination;
            $cl->deal_id = $request->deal_id;

            $cl->status = 1;
            $cl->company_id = session('logged_session_data.company_id');
            $cl->created_by = Auth::user()->id;
            $cl->save();
            $cl->toArray();


            for ($i = 0; $i < count($request->part_number); $i++) {
                if ($request->qty[$i] != "" && $request->qty[$i] > 0) {
                    $cli = new SysClearanceItems();
                    $cli->clearance_id = $cl->id;
                    $cli->pid = $request->part_number[$i] ?? null;
                    $cli->partno = $request->part_number_txt[$i] ?? null;
                    $cli->description = $request->description[$i] ?? null;
                    $cli->coo = $request->coo[$i] ?? null;
                    $cli->hscode = $request->hscode[$i] ?? null;
                    $cli->weight = $request->weight[$i] ?? null;
                    $cli->qty = $request->qty[$i] ?? null;
                    $cli->price = $request->unitprice[$i] ?? null;
                    $cli->totalprice = $request->value[$i] ?? null;
                    $cli->status = 1;
                    $cli->created_by = Auth::user()->id;
                    $cli->save();
                }


            }

            SysCrmDeals::where('id', $cl->deal_id)->update([
                'clearance_id' => $cl->id,
            ]);


            // if (count($cart) > 0) {
            //     foreach ($cart as $dt) {
            //         $cli = new SysClearanceItems();
            //         $cli->clearance_id = $cl->id;
            //         $cli->pid = $dt->pid;
            //         $cli->partno = $dt->partno;
            //         $cli->description = $dt->description;
            //         $cli->coo = $dt->coo;
            //         $cli->hscode = $dt->hscode;
            //         $cli->weight = $dt->weight;
            //         $cli->qty = $dt->qty;
            //         $cli->price = $dt->price;
            //         $cli->totalprice = $dt->totalprice;
            //         $cli->status = 1;
            //         $cli->created_by = Auth::user()->id;
            //         $cli->save();
            //     }
            //     DB::table('sys_clearance_items_cart')->where('cart_id', session('logged_session_data.cart_id'))->delete();
            //     SysCrmDeals::where('id', $cl->deal_id)->update([
            //         'clearance_id' => $cl->id,
            //     ]);
            // }


            DB::table('sys_clearance_att')->where('cart_id', session('logged_session_data.cart_id'))->where('doc_id', 0)->where('company_id', session('logged_session_data.company_id'))->update(['doc_id' => $cl->id]);

            DB::commit();
            Toastr::success('Clearance Added Successful', 'Success');
            return back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return $e;
            return redirect()->back();
        }
    }
    //end store method 

    public function preview($id)
    {
        try {
            $cl = SysClearance::find($id);

            if (!empty($cl)) {
                $cl_item = SysClearanceItems::where('clearance_id', '=', $cl->id)->get();

                $total = DB::table('sys_clearance_items')
                    ->select(DB::raw('SUM(weight) AS weight1'), DB::raw('SUM(qty) AS qty1'), DB::raw('SUM(price) AS price1'), DB::raw('SUM(totalprice) AS totalprice1'))
                    ->where('clearance_id', '=', $cl->id)->get();

                $coos = DB::table('sys_clearance_items')->select(DB::raw('DISTINCT coo'))->where('clearance_id', '=', $cl->id)->get();
                $currency = DB::table('sys_currency')->select('code')->where('id', '=', $cl->currency)->first();

                $data = [
                    'cl' => $cl,
                    'cl_item' => $cl_item,
                    'total' => $total,
                    'coos' => $coos,
                    'currency' => $currency,
                ];

                return view('backEnd.clearance.clearance_preview', $data);
                //$pdf = PDF::loadView('backEnd.clearance.clearance_pdf', $data);
                //$pdf->setPaper('A4', 'portrait');
                //return $pdf->download("Clearance_".$cl->invoice_no.".pdf");
            } else {
                return "error!!";
                //return view('web.syscom_credit_application_form');
            }
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function download($id)
    {
        try {
            $cl = SysClearance::find($id);

            if (!empty($cl)) {
                $cl_item = SysClearanceItems::where('clearance_id', '=', $cl->id)->orderby('partno', 'ASC')->get();

                $total = DB::table('sys_clearance_items')
                    ->select(DB::raw('SUM(weight) AS weight1'), DB::raw('SUM(qty) AS qty1'), DB::raw('SUM(price) AS price1'), DB::raw('SUM(totalprice) AS totalprice1'))
                    ->where('clearance_id', '=', $cl->id)->get();

                $coos = DB::table('sys_clearance_items')->select(DB::raw('DISTINCT coo'))->where('clearance_id', '=', $cl->id)->get();
                $currency = DB::table('sys_currency')->select('code')->where('id', '=', $cl->currency)->first();

                $data = [
                    'cl' => $cl,
                    'cl_item' => $cl_item,
                    'total' => $total,
                    'coos' => $coos,
                    'currency' => $currency,
                ];
                $pdf = PDF::loadView('backEnd.clearance.clearance_pdf', $data);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download("Clearance_" . $cl->invoice_no . ".pdf");
            } else {
                return "error!!";
                //return view('web.syscom_credit_application_form');
            }
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function edit($id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $currency = SysCurrencySettings::all();

            $clearance = SysClearance::where('id', $id)->first();

            $clearanceitems = SysClearanceItems::where('clearance_id', '=', $id)->get();
            $total = DB::table('sys_clearance_items')
                ->select(DB::raw('SUM(weight) AS weight1'), DB::raw('SUM(qty) AS qty1'), DB::raw('SUM(price) AS price1'), DB::raw('SUM(totalprice) AS totalprice1'))
                ->where('clearance_id', '=', $id)->get();
            return compact('clearance', 'clearanceitems', 'total', 'currency');

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            //return redirect()->back(); 
            return $e;
        }
    }
    public function update(Request $request)
    {
        
        DB::beginTransaction();
        try {
            $cl = SysClearance::find($request->id);
            $cl->doc_no = $request->doc_no;
            $cl->invoice_no = $request->invoice_no;
            $cl->invoice_date = SysHelper::normalizeToYmd($request->invoice_date);
            $cl->currency = $request->currency;
            $cl->free_zone_bill_no = $request->free_zone_bill_no;
            $cl->goods_description = $request->goods_description;
            $cl->bill_to = $request->bill_to;
            $cl->bill_to_address = "";
            $cl->ship_to = $request->ship_to;
            $cl->ship_to_address = $request->ship_to_address;
            $cl->boe_no = $request->boe_no;
            $cl->payment_method = implode(', ', $request->payment_method);
            $cl->customer_bill_type = implode(', ', $request->customer_bill_type);

            $cl->box_type = $request->box_type;
            $cl->box_qty = $request->box_qty;
            $cl->cbm = $request->cbm;
            $cl->exit_point = $request->exit_point;
            $cl->destination = $request->destination;

            $cl->status = 1;
            $cl->company_id = session('logged_session_data.company_id');
            $cl->updated_by = Auth::user()->id;
            $cl->save();
            $cl->toArray();

            if (count($request->part_number) > 0) {

                SysClearanceItems::where('clearance_id',$cl->id)->delete();

                for ($i = 0; $i < count($request->part_number); $i++) {
                    if ($request->qty[$i] != "" && $request->qty[$i] > 0) {
                        $cli = new SysClearanceItems();
                        $cli->clearance_id = $cl->id;
                        $cli->pid = $request->part_number[$i] ?? null;
                        $cli->partno = $request->part_number_txt[$i] ?? null;
                        $cli->description = $request->description[$i] ?? null;
                        $cli->coo = $request->coo[$i] ?? null;
                        $cli->hscode = $request->hscode[$i] ?? null;
                        $cli->weight = $request->weight[$i] ?? null;
                        $cli->qty = $request->qty[$i] ?? null;
                        $cli->price = $request->unitprice[$i] ?? null;
                        $cli->totalprice = $request->value[$i] ?? null;
                        $cli->status = 1;
                        $cli->created_by = Auth::user()->id;
                        $cli->save();
                    }
                }
                SysCrmDeals::where('id', $cl->deal_id)->update([
                    'clearance_id' => $cl->id,
                ]);
            }



            //DB::table('sys_clearance_items')->where('clearance_id', $request->id)->delete();

            /*for($i = 0; $i < count($request->part_number); $i++) {
                if($request->part_number[$i] !="none" && $request->qty[$i] !="" && $request->price[$i] !=""){
                    $cli = new SysClearanceItems();
                    $cli->clearance_id = $cl->id;
                    $cli->pid = $request->part_number[$i];
                    $cli->partno = $request->partno[$i];
                    $cli->description = $request->description[$i];
                    $cli->coo = $request->coo[$i];
                    $cli->hscode = $request->hscode[$i];
                    $cli->weight = $request->weight[$i];
                    $cli->qty = $request->qty[$i];
                    $cli->price = $request->price[$i];
                    $cli->totalprice = $request->totalprice[$i];

                    //$cli->discount = ($request->discount[$i] === '' ? '0.00' : $request->discount[$i]);
                    //$cli->customcharges = ($request->customcharges[$i] === '' ? '0.00' : $request->customcharges[$i]);

                    $cli->status = 1;
                    $cli->created_by = Auth::user()->id;
                    $cli->save();
                }
            }*/

            DB::commit();
            Toastr::success('Clearance Updated Successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return $e;
            return redirect()->back();
        }
    }

    function add_deal_items_to_clearance_cart(Request $request)
    {
        try {
            $deal = SysCrmDeals::where('id', $request->clearance_deal_id)->first();
            $customer_address = $deal->customername->address;
            //$salesman_name = $deal->ownername->full_name;
            $invoice_no = DB::table('sys_crm_deal_track_approval_invoice')->where('deal_id', $request->clearance_deal_id)->max('invoice_no');

            $invoice_no = str_replace('/', ',', $invoice_no);

            $deal_id = $deal->id;
            $tax = SysHelper::get_company_tax($deal->company_id);
            $account_id = SysHelper::get_company_account_id($deal->cust_id);

            $deal_items = SysCrmQuoteItems::select('sys_crm_quote_items.*', 'sm_items.part_number', 'sm_items.coo', 'sm_items.hscode', 'sm_items.weight')
                ->join('sm_items', 'sm_items.id', 'sys_crm_quote_items.product_id')
                ->where('deal_id', $request->clearance_deal_id)->get();
            DB::table('sys_clearance_items_cart')->where(['cart_id' => session('logged_session_data.cart_id'), 'deal_id' => $request->clearance_deal_id])->delete();

            foreach ($deal_items as $items) {

                /*$check_cart = DB::table('sys_clearance_items_cart')->where([
                    'cart_id' => session('logged_session_data.cart_id'),
                    'pid' => $items->product_id,
                    'price' => $items->price,
                    'qty' => $items->qty,
                    'deal_id' => $request->clearance_deal_id,
                ])->count();*/

                //if($check_cart == 0){
                DB::table('sys_clearance_items_cart')->insert([
                    'cart_id' => session('logged_session_data.cart_id'),
                    'pid' => $items->product_id,
                    'partno' => $items->part_number,
                    'description' => $items->description,
                    'coo' => $items->coo,
                    'hscode' => $items->hscode,
                    'weight' => $items->weight * $items->qty,
                    'qty' => $items->qty,
                    'price' => $items->price,
                    'totalprice' => $items->price * $items->qty,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                    'deal_id' => $request->clearance_deal_id,
                ]);
                //}
            }

            return redirect('clearance-add/' . $invoice_no . '/' . $account_id . '/' . $deal_id);



        } catch (\Exception $e) {
            return $e;
        }
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
            //return $po_item;
            $data = [
                'si' => $si,
                'company' => $company,
                'si_item' => $si_item,
            ];
            $pdf = PDF::loadView('backEnd.pdf_print.si_pdf', $data);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download("purchase_invoice_" . $si->doc_number . ".pdf");
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



    public function update123(Request $request)
    {
        //return $request;
        //return $request;
        // $input = $request->all();
        // if ($request->work_order_mode == "equipment") {
        //     $validator = Validator::make($input, [
        //         'title'    => 'required',
        //         'number'       => 'required',
        //         'date'   => 'required',
        //         // 'vendors'        => 'required|not_in:0',
        //         'customer'        => 'required|not_in:0',
        //         'quotation_type' => 'required',
        //     ]);
        // } else {
        //     $validator = Validator::make($input, [
        //         'title'    => 'required',
        //         'number'       => 'required',
        //         'date'   => 'required',
        //         // 'vendors'        => 'required|not_in:0',
        //         'customer'        => 'required|not_in:0',
        //         'quotation_type' => 'required',
        //     ]);
        // }
        // if ($validator->fails()) {
        //     return redirect('post/create')->withErrors($validator)->withInput();
        // }


        DB::beginTransaction();
        try {

            $si = SysSalesInvoice::find($request->id);
            $si->si_date = date('Y-m-d', strtotime($request->si_date));
            $si->customer = $request->customer;
            $si->currency = $request->currency;
            $si->narration = $request->narration;
            $si->lpo_number = $request->lpo_number;
            $si->lpo_date = date('Y-m-d', strtotime($request->lpo_date));
            $si->bill_number = $request->bill_number;
            $si->bill_date = date('Y-m-d', strtotime($request->bill_date));
            $si->payment_terms = $request->payment_terms;
            $si->payment_terms2 = $request->payment_terms2;
            $si->supplier_remarks = $request->supplier_remarks;

            // $pi->shipping_address_1 = $request->shipping_address_1;
            // $pi->shipping_address_2 = $request->shipping_address_2;
            // $pi->shipping_name = $request->shipping_name;
            // $pi->shipping_contact_no = $request->shipping_contact_no;

            $si->supplier_type = $request->supplier_type;
            $si->purchase_type = $request->purchase_type;
            $si->supplier_country = $request->supplier_country;
            $si->supplier_state = $request->supplier_state;

            $si->delivery = $request->delivery;
            $si->printed_invoice_number = $request->printed_invoice_number;
            $si->salesman = $request->salesman;
            $si->end_user_name = $request->end_user_name;
            $si->contact_person_name = $request->contact_person_name;
            $si->contact_person_email = $request->contact_person_email;
            $si->contact_person_no = $request->contact_person_no;
            $si->note = $request->note;
            $si->status = 1;
            $si->company_id = session('logged_session_data.company_id');
            $si->updated_by = Auth::user()->id;
            $si->save();
            $si->toArray();

            SysSalesInvoiceItems::query()
                ->where('si_id', '=', $request->id)
                ->each(function ($oldRecord) {
                    $newRecord = $oldRecord->replicate();
                    $newRecord->setTable('sys_sales_invoice_items_history');
                    $newRecord->save();
                    $oldRecord->delete();
                });

            for ($i = 0; $i < count($request->part_number); $i++) {
                if ($request->part_number[$i] != "" && $request->qty[$i] != "" && $request->unitprice[$i] != "") {
                    $sii = new SysSalesInvoiceItems();
                    $sii->si_id = $request->id;
                    $sii->part_number = $request->part_number[$i];
                    $sii->tax = $request->net_vat;
                    $sii->qty = $request->qty[$i];
                    $sii->unitprice = $request->unitprice[$i];
                    $sii->value = $request->value[$i];
                    $sii->discount = ($request->discount[$i] === '' ? '0.00' : $request->discount[$i]);
                    $sii->customcharges = ($request->customcharges[$i] === '' ? '0.00' : $request->customcharges[$i]);
                    $sii->taxableamount = $request->taxableamount[$i];
                    $sii->vatamount = $request->vatamount[$i];
                    $sii->status = 1;
                    $sii->created_by = Auth::user()->id;
                    $sii->save();
                }
            }

            for ($i = 0; $i < count($request->cfc_name); $i++) {
                if ($request->cfc_name[$i] != "" && $request->cfc_credit_account[$i] != "" && $request->cfc_amount[$i] != "") {
                    $cfc = new SysSalesInvoiceCFCharges();
                    $cfc->si_id = $request->id;
                    $cfc->cfc_name = $request->cfc_name[$i];
                    $cfc->cfc_credit_account = $request->cfc_credit_account[$i];
                    $cfc->cfc_amount = $request->cfc_amount[$i];
                    $cfc->cfc_cal_amount = $request->cfc_cal_amount[$i];
                    $cfc->cfc_remarks = $request->cfc_remarks[$i];
                    $cfc->cfc_currency = $request->cfc_currency[$i];
                    $cfc->cfc_exe_rate = $request->cfc_exe_rate[$i];
                    $cfc->status = 1;
                    $cfc->created_by = Auth::user()->id;
                    $cfc->save();
                }
            }



            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect('sales-invoice/' . $request->id . '/edit');

        } catch (\Exception $e) {

            return $e;

            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
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
            $result = SmQuotation::destroy($id);

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

    function add_attachment(Request $request)
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
                'doc_id' => $request->doc_id,
                'doc_file' => $selected_file,
                'doc_date' => SysHelper::normalizeToYmd($request->att_date),
                'doc_name' => $request->doc_name,
                'status' => 1,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now('+04:00'),
                'company_id' => session('logged_session_data.company_id'),
            ];

            DB::table('sys_clearance_att')->insert($data);

            if ($request->doc_id == 0) {
                $ret = DB::table('sys_clearance_att')->where('doc_id', $request->doc_id)->where('cart_id', session('logged_session_data.cart_id'))->where('company_id', session('logged_session_data.company_id'))->get();
            } else {
                $ret = DB::table('sys_clearance_att')->where('doc_id', $request->doc_id)->where('company_id', session('logged_session_data.company_id'))->get();
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
    function view_attachment(Request $request)
    {
        try {

            if ($request->doc_id == 0) {
                $ret = DB::table('sys_clearance_att')->where('doc_id', $request->doc_id)->where('cart_id', session('logged_session_data.cart_id'))->where('company_id', session('logged_session_data.company_id'))->get();
            } else {
                $ret = DB::table('sys_clearance_att')->where('doc_id', $request->doc_id)->where('company_id', session('logged_session_data.company_id'))->get();
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
    function delete_attachment(Request $request)
    {
        try {
            DB::table('sys_clearance_att')->where('id', $request->id)->delete();

            if ($request->doc_id == 0) {
                $ret = DB::table('sys_clearance_att')->where('doc_id', $request->doc_id)->where('cart_id', session('logged_session_data.cart_id'))->where('company_id', session('logged_session_data.company_id'))->get();
            } else {
                $ret = DB::table('sys_clearance_att')->where('doc_id', $request->doc_id)->where('company_id', session('logged_session_data.company_id'))->get();
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

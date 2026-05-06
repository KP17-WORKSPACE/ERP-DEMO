<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SmInspectingDepartment;
use App\SmItem;
use Illuminate\Http\Request;
use App\SmItemStore;
use App\SysChartofAccounts;
use App\SysCompany;
use App\SysCurrency;
use App\SysCurrencySettings;
use App\SysCustSuppl;
use App\SysHelper;
use App\SysItemOpeningStock;
use App\SysItemStock;
use App\SysPaymentTerms;
use App\SysSalesInvoiceItemsCart;
use App\SysShipping;
use App\SysStockIn;
use App\SysStockInItems;
use App\SysStockInItemsCart;
use App\SysStockInSerialNo;
use App\SysSupplierType;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;
use Validator;
use PHPExcel;
use PHPExcel_IOFactory;

class SysStockInController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function getCreateData()
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $currency = SysCurrency::select('id', 'code')->get();
            $items = SysHelper::get_product_list($company_id);
            $cart = SysStockInItemsCart::select('sys_stock_in_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_stock_in_items_cart.part_number')
                ->where('cart_id', session('logged_session_data.cart_id'))->get();
            return compact('currency', 'items', 'cart');
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function index(Request $request, $id = null)
    {
        try {
            $data = SysStockIn::where('company_id', session('logged_session_data.company_id'))->get();

            $active_id = $id;

            $selectedStock = null;
            $action = null;
            $editData = null;
            $createData = null;

            $customers = [];
            $suppliers = [];


            if ($request->has('stockin_action')) {
                $StockINAction = $request->input('stockin_action');

                if ($StockINAction === 'add') {
                    $action = 'add';

                    $customers = SysHelper::get_customer_list(null);
                    $suppliers = SysHelper::get_supplier_list(null);

                    $r = SysHelper::get_data_by_role();
                    $company_id = $r[0];




                    $createData = $this->getCreateData();
                } elseif ($StockINAction === 'edit') {
                    $action = 'edit';
                    $editData = $this->edit($active_id); // Get all data for editing
                }
            } else {

                if ($id) {
                    $selectedStock = $this->get_print_data($id);
                } else {
                    $firstRecord = $data->first();
                    if ($firstRecord) {
                        $active_id = $firstRecord->id;
                        $selectedStock = $this->get_print_data($firstRecord->id);
                    }
                }
                // if ($active_id)
                //     $editData = $this->edit($active_id); // Get all data for editing
                // else {
                //     $editData = $this->edit($data->first()->id); // Get all data for editing
                // }
            }


            // if ($request->has('stockin_action')) {
            //     $StockINAction = $request->input('stockin_action');

            //     if ($StockINAction === 'add') {
            //         $action = 'add';

            //         $createData = $this->getCreateData();

            //     } elseif ($StockINAction === 'edit') {
            //         $action = 'edit';
            //         // $editData = $this->edit($active_id); // Get all data for editing
            //     }
            // } else {
            //     if ($id) {
            //         $selectedStock = SysStockIn::where('id', $id)->first();
            //     } else {
            //         $firstRecord = $data->first();
            //         if ($firstRecord) {
            //             $active_id = $firstRecord->id;
            //             $selectedStock = SysStockIn::where('id', $active_id)->first();
            //         }
            //     }
            // }



            return view('backEnd.inventory.StockInList', compact('data', 'selectedStock', 'active_id', 'action', 'editData', 'createData', 'customers', 'suppliers'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            //return redirect()->back();
            return $e;
        }
    }


    public function get_print_data($id)
    {
        try {
            $edit = SysStockIn::where('id', $id)->first();
            $company = SysCompany::find($edit->company_id);
            $edit_items = SysStockInItems::select('sys_stock_in_items.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_stock_in_items.part_number')
                ->where('stock_in_id', $id)->get();

            return compact('edit', 'edit_items', 'company');
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }
    public function store(Request $request)
    {


        DB::beginTransaction();
        try {

            // $cart = SysStockInItemsCart::select('sys_stock_in_items_cart.*', 'sm_items.part_number AS partno')
            //     ->join('sm_items', 'sm_items.id', 'sys_stock_in_items_cart.part_number')
            //     ->where('cart_id', session('logged_session_data.cart_id'))->get();
            // if (count($cart) > 0) {

            // } else {
            //     Toastr::error('Items not found', 'Failed');
            //     return redirect()->back();
            // }

            $customer_name = "";
            $supplier_name = "";

            $ssi = new SysStockIn();
            $ssi->date = SysHelper::normalizeToYmd($request->date);

            if ($request->mode == "ES") { //mode 1 cash, mode 2 bank
                $ssi->doc_number = SysHelper::get_new_code('sys_stock_in', 'EX', 'doc_number');

            } elseif ($request->mode == "DI") {
                $ssi->doc_number = SysHelper::get_new_code('sys_stock_in', 'DI', 'doc_number');
            } else {
                $ssi->doc_number = SysHelper::get_new_code('sys_stock_in', 'RI', 'doc_number');
                $ssi->customer_id = !empty($request->customer_id) ? $request->customer_id : null;
                $ssi->supplier_id = !empty($request->supplier_id) ? $request->supplier_id : null;


                if ($request->customer_id) {
                    $customer_name = SysChartofAccounts::where('id', $request->customer_id)
                        ->value('account_name');
                }
                if ($request->supplier_id) {
                    $supplier_name = SysChartofAccounts::where('id', $request->supplier_id)
                        ->value('account_name');
                }
            }
            $ssi->remarks = $request->remarks;
            $ssi->currancy = $request->currency;
            $ssi->status = 1;
            $ssi->company_id = session('logged_session_data.company_id');
            $ssi->created_by = Auth::user()->id;
            $ssi->created_at = Carbon::now('+04:00');
            $ssi->save();
            $ssi->toArray();

            $draftStockInLicenseScope = DB::table('sys_purchase_grn_license_key')
                ->where('type', 3)
                ->where('opening_stock_id', -1)
                ->where('cart_id', session('logged_session_data.cart_id'))
                ->where('company_id', session('logged_session_data.company_id'));
            if ((clone $draftStockInLicenseScope)->count() > 0) {
                (clone $draftStockInLicenseScope)->update([
                    'opening_stock_id' => $ssi->id,
                    'cart_id' => '',
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]);
            }



            for ($i = 0; $i < count($request->part_number); $i++) {
                if ($request->qty[$i] != "" && $request->qty[$i] > 0) {
                    $sii = new SysStockInItems();
                    $sii->stock_in_id = $ssi->id;
                    $sii->part_number = $request->part_number[$i] ?? null;
                    $sii->description = $request->description[$i] ?? null;
                    $sii->qty = str_replace(',', '',$request->qty[$i]);
                    $sii->unitprice = str_replace(',', '',$request->unitprice[$i]) ?? 0;
                    $sii->value = str_replace(',', '',$request->value[$i]) ?? 0;
                    $sii->serialno = $request->serial_no[$i] ?? '';
                    $sii->narration = $request->narration[$i] ?? '';
                    $sii->status = 1;
                    $sii->created_by = Auth::user()->id;
                    $sii->save();

                    $str_arr = explode(",", $request->serial_no[$i]);
                    foreach ($str_arr as $srl) {
                        $values = array('in_id' => $ssi->id, 'pid' => $request->part_number[$i], 'serial_no' => $srl);
                        DB::table('sys_stock_in_serial_no')->insert($values);
                    }

                    $istock = new SysItemStock();
                    $istock->stock_in_id = $ssi->id;
                    $istock->account_id = 0;
                    $istock->partno = $request->part_number[$i];
                    $istock->qty_in = str_replace(',', '',$request->qty[$i]);
                    $istock->price_in = str_replace(',', '',$request->unitprice[$i]) ?? 0;
                    $istock->refno = $ssi->doc_number;
                    $istock->doc_number = $ssi->doc_number;
                    $istock->doc_date = $ssi->date;
                    $istock->deal_id = 0;
                    $istock->slno = $request->serial_no[$i] ?? '';
                    $istock->status = 1;
                    if ($request->mode == "RI")
                        $istock->description = $customer_name . " " . $supplier_name;
                    $istock->created_by = Auth::user()->id;
                    $istock->company_id = session('logged_session_data.company_id');
                    $istock->currency_id = $ssi->currency;
                    $istock->save();
                }
            }

            // foreach ($cart as $items) {
            //     $sii = new SysStockInItems();
            //     $sii->stock_in_id = $ssi->id;
            //     $sii->part_number = $items->part_number;
            //     $sii->description = $items->description;
            //     $sii->qty = $items->qty;
            //     $sii->unitprice = $items->unitprice;
            //     $sii->value = $items->value;
            //     $sii->serialno = $items->serialno;
            //     $sii->narration = $items->narration;
            //     $sii->status = 1;
            //     $sii->created_by = Auth::user()->id;
            //     $sii->save();

            //     $str_arr = explode(",", $items->serialno);
            //     foreach ($str_arr as $srl) {
            //         $values = array('in_id' => $ssi->id, 'pid' => $items->part_number, 'serial_no' => $srl);
            //         DB::table('sys_stock_in_serial_no')->insert($values);
            //     }

            //     $istock = new SysItemStock();
            //     $istock->stock_in_id = $ssi->id;
            //     $istock->account_id = 0;
            //     $istock->partno = $items->part_number;
            //     $istock->qty_in = $items->qty;
            //     $istock->price_in = $items->unitprice;
            //     $istock->refno = $ssi->doc_number;
            //     $istock->doc_number = $ssi->doc_number;
            //     $istock->doc_date = $ssi->date;
            //     $istock->deal_id = 0;
            //     $istock->slno = $items->serialno;
            //     $istock->status = 1;
            //     $istock->created_by = Auth::user()->id;
            //     $istock->company_id = session('logged_session_data.company_id');
            //     $istock->currency_id = $ssi->currency;
            //     $istock->save();
            // }

            SysStockInItemsCart::where('cart_id', session('logged_session_data.cart_id'))->delete();
            $results = 0;
            DB::commit();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($results == 0) {
                    return ApiBaseMethod::sendResponse(null, 'Stock In has been added successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } else {
                if ($results == 0) {
                    Toastr::success('Stock In has been added successfully', 'Success');
                    return redirect('stock-in/'.$ssi->id);
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }

        } catch (\Exception $e) {
            return $e;
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->index(request(), $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    // public function view(Request $request, $id)
    // {
    //     try {
    //         $r = SysHelper::get_data_by_role();
    //         $company_id = $r[0];
    //         $currency = SysCurrency::select('id', 'code')->get();
    //         $items = SysHelper::get_product_list($company_id);
    //         $edit = SysStockIn::where('id', $id)->first();
    //         $edit_items = SysStockInItems::select('sys_stock_in_items.*', 'sm_items.part_number AS partno')
    //             ->join('sm_items', 'sm_items.id', 'sys_stock_in_items.part_number')
    //             ->where('stock_in_id', $id)->get();
    //         return view('backEnd.inventory.StockInFormView', compact('currency', 'items', 'edit', 'edit_items'));
    //     } catch (\Exception $e) {
    //         return $e;
    //         Toastr::error('Operation Failed', 'Failed');
    //         return redirect()->back();
    //     }
    // }

    public function view(Request $request, $id)
    {
        try {
            $edit = SysStockIn::where('id', $id)->first();
            $company = SysCompany::find($edit->company_id);
            $edit_items = SysStockInItems::select('sys_stock_in_items.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_stock_in_items.part_number')
                ->where('stock_in_id', $id)->get();
            return view('backEnd.inventory.StockInFormView', compact('company', 'edit', 'edit_items'));
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
            $currency = SysCurrency::select('id', 'code')->get();
            $items = SysHelper::get_product_list($company_id);
            $edit = SysStockIn::where('id', $id)->first();

            $customers = SysHelper::get_customer_list(null);
            $suppliers = SysHelper::get_supplier_list(null);

            $mode = null;



            // Take first 2 letters
            $prefix = substr($edit->doc_number, 0, 2);

            switch ($prefix) {
                case 'RI':
                    $mode = 'RI';
                    break;
                case 'DI':
                    $mode = 'DI';
                    break;
                case 'EX':
                    $mode = 'ES';
                    break;
                default:
                    $mode = 'Unknown';
            }





            $edit_items = SysStockInItems::select('sys_stock_in_items.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_stock_in_items.part_number')
                ->where('stock_in_id', $id)->get();
            return compact('currency', 'items', 'edit', 'edit_items', 'mode', 'customers', 'suppliers');
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    // public function edit(Request $request, $id)
    // {
    //     try {
    //         $r = SysHelper::get_data_by_role();
    //         $company_id = $r[0];
    //         $currency = SysCurrency::select('id', 'code')->get();
    //         $items = SysHelper::get_product_list($company_id);
    //         $edit = SysStockIn::where('id', $id)->first();
    //         $edit_items = SysStockInItems::select('sys_stock_in_items.*', 'sm_items.part_number AS partno')
    //             ->join('sm_items', 'sm_items.id', 'sys_stock_in_items.part_number')
    //             ->where('stock_in_id', $id)->get();
    //         return view('backEnd.inventory.StockInFormEdit', compact('currency', 'items', 'edit', 'edit_items'));
    //     } catch (\Exception $e) {
    //         return $e;
    //         Toastr::error('Operation Failed', 'Failed');
    //         return redirect()->back();
    //     }
    // }
    public function items_add(Request $request)
    {
        try {
            DB::table('sys_stock_in_items')->insert(
                [
                    'stock_in_id' => $request->stock_in_id,
                    'part_number' => $request->part_number,
                    'description' => $request->description,
                    'qty' => $request->qty,
                    'unitprice' => $request->unitprice,
                    'value' => $request->value,
                    'serialno' => $request->serialno,
                    'narration' => $request->narration,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );
            $ret = SysStockInItems::select('sys_stock_in_items.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_stock_in_items.part_number')
                ->where('stock_in_id', $request->stock_in_id)->get();
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
    public function items_update(Request $request)
    {
        try {
            DB::table('sys_stock_in_items')->where('id', $request->itm_id)->update([
                'part_number' => $request->part_number,
                'description' => $request->description,
                'qty' => str_replace(',', '', $request->qty),
                'unitprice' => str_replace(',', '', $request->unitprice),
                'value' => str_replace(',', '', $request->value),
                'serialno' => $request->serialno,
                'narration' => $request->narration,
            ]);

            $ret = SysStockInItems::select('sys_stock_in_items.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_stock_in_items.part_number')
                ->where('stock_in_id', $request->stock_in_id)->get();

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
    public function items_delete(Request $request)
    {
        try {
            DB::table('sys_stock_in_items')->where('id', $request->id)->delete();
            $ret = SysStockInItems::select('sys_stock_in_items.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_stock_in_items.part_number')
                ->where('stock_in_id', $request->stock_in_id)->get();

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


    public function update(Request $request, $id)
    {


        DB::beginTransaction();
        try {

            // $cart = SysStockInItems::select('sys_stock_in_items.*', 'sm_items.part_number AS partno')
            //     ->join('sm_items', 'sm_items.id', 'sys_stock_in_items.part_number')
            //     ->where('stock_in_id', $id)->get();
            // if (count($cart) > 0) {

            // } else {
            //     Toastr::error('Items not found', 'Failed');
            //     return redirect()->back();
            // }

            $ssi = SysStockIn::find($id);
            $customer_name = "";
            $supplier_name = "";

            $mode_change_remarks = "";



            if ($request->mode != $request->current_mode) {
                if ($request->mode == "ES") { //mode 1 cash, mode 2 bank
                    $doc = SysHelper::get_new_code('sys_stock_in', 'EX', 'doc_number');
                } elseif ($request->mode == "DI") {
                    $doc = SysHelper::get_new_code('sys_stock_in', 'DI', 'doc_number');
                } else {
                    $doc = SysHelper::get_new_code('sys_stock_in', 'RI', 'doc_number');
                }

                $mode_change_remarks = $ssi->doc_number . " change to " . $doc;
                $ssi->doc_number = $doc;
            }

            if ($request->mode == "RI") {

                $ssi->customer_id = !empty($request->customer_id) ? $request->customer_id : null;
                $ssi->supplier_id = !empty($request->supplier_id) ? $request->supplier_id : null;


                if ($request->customer_id) {
                    $customer_name = SysChartofAccounts::where('id', $request->customer_id)
                        ->value('account_name');
                }
                if ($request->supplier_id) {
                    $supplier_name = SysChartofAccounts::where('id', $request->supplier_id)
                        ->value('account_name');
                }
            }



            $ssi->date = $ssi->date = SysHelper::normalizeToYmd($request->date);
            if ($mode_change_remarks != "") {
                $ssi->remarks = $mode_change_remarks . " / " . $request->remarks;
            } else {
                $ssi->remarks = $request->remarks;
            }
            $ssi->currancy = $request->currency;
            $ssi->status = 1;
            $ssi->updated_by = Auth::user()->id;
            $ssi->updated_at = Carbon::now('+04:00');

            $ssi->save();

            $ssi->toArray();

            DB::table('sys_stock_in_serial_no')->where('in_id', $ssi->id)->delete();
            DB::table('sys_item_stock')->where('stock_in_id', $ssi->id)->delete();
            DB::table('sys_stock_in_items')->where('stock_in_id', $ssi->id)->delete();


            for ($i = 0; $i < count($request->part_number); $i++) {
                if ($request->qty[$i] != "" && $request->qty[$i] > 0) {
                    $sii = new SysStockInItems();
                    $sii->stock_in_id = $ssi->id;
                    $sii->part_number = $request->part_number[$i] ?? null;
                    $sii->description = $request->description[$i] ?? null;
                    $sii->qty = str_replace(',', '', $request->qty[$i]);
                    $sii->unitprice = str_replace(',', '', $request->unitprice[$i]) ?? 0;
                    $sii->value = str_replace(',', '', $request->value[$i]) ?? 0;
                    $sii->serialno = $request->serial_no[$i] ?? '';
                    $sii->narration = $request->narration[$i] ?? '';
                    $sii->status = 1;
                    $sii->created_by = Auth::user()->id;
                    $sii->save();

                    $str_arr = explode(",", $request->serial_no[$i]);
                    foreach ($str_arr as $srl) {
                        $values = array('in_id' => $ssi->id, 'pid' => $request->part_number[$i], 'serial_no' => $srl);
                        DB::table('sys_stock_in_serial_no')->insert($values);
                    }

                    $istock = new SysItemStock();
                    $istock->stock_in_id = $ssi->id;
                    $istock->account_id = 0;
                    $istock->partno = $request->part_number[$i];
                    $istock->qty_in = str_replace(',', '', $request->qty[$i]);
                    $istock->price_in = str_replace(',', '', $request->unitprice[$i]) ?? 0;
                    $istock->refno = $ssi->doc_number;
                    $istock->doc_number = $ssi->doc_number;
                    $istock->doc_date = $ssi->date;
                    $istock->deal_id = 0;
                    $istock->slno = $request->serial_no[$i] ?? '';
                    $istock->status = 1;
                    if ($request->mode == "RI")
                        $istock->description = $customer_name . " " . $supplier_name;
                    $istock->created_by = Auth::user()->id;
                    $istock->company_id = session('logged_session_data.company_id');
                    $istock->currency_id = $ssi->currency;
                    $istock->save();
                }
            }




            // foreach ($cart as $items) {

            //     $str_arr = explode(",", $items->serialno);
            //     foreach ($str_arr as $srl) {
            //         $values = array('in_id' => $ssi->id, 'pid' => $items->part_number, 'serial_no' => $srl);
            //         DB::table('sys_stock_in_serial_no')->insert($values);
            //     }

            //     $istock = new SysItemStock();
            //     $istock->stock_in_id = $ssi->id;
            //     $istock->account_id = 0;
            //     $istock->partno = $items->part_number;
            //     $istock->qty_in = $items->qty;
            //     $istock->price_in = $items->unitprice;
            //     $istock->refno = $ssi->doc_number;
            //     $istock->doc_number = $ssi->doc_number;
            //     $istock->doc_date = $ssi->date;
            //     $istock->deal_id = 0;
            //     $istock->slno = $items->serialno;
            //     $istock->status = 1;
            //     $istock->created_by = Auth::user()->id;
            //     $istock->company_id = $ssi->company_id;
            //     $istock->currency_id = $ssi->currency;
            //     $istock->save();
            // }

            // SysStockInItemsCart::where('cart_id', session('logged_session_data.cart_id'))->delete();
            $results = 0;
            DB::commit();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {

                if ($results == 0) {
                    return ApiBaseMethod::sendResponse(null, 'Stock In has been updated successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } else {

                if ($results == 0) {

                    Toastr::success('Stock In has been updated successfully', 'Success');
                    return redirect('stock-in/'.$id);
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }

        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }

    public function destroy($id)
    {
        //
    }

    public function cart_excel_add(Request $request)
    {

        try {

            DB::beginTransaction();
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

                          

                     
                        DB::table('sys_stock_in_items_cart')->insert(
                            [
                                'cart_id' => session('logged_session_data.cart_id'),
                                'part_number' => $pid,
                                'description' => $description,
                                'qty' => $request->excel_qty[$i],
                                'unitprice' => $request->excel_unit_price[$i],
                                'value' => $request->excel_unit_price[$i] * $request->excel_qty[$i],
                                'serialno' => $request->excel_serial_no[$i],
                                'narration' => $request->excel_narration[$i],
                                'status' => 1,
                                'created_by' => Auth::user()->id,
                                'created_at' => Carbon::now('+04:00'),
                            ]
                        );
                        }

                         
                    }
                }
            }
            DB::commit();
            Toastr::success('Item Imported Successfully', 'Success');
            return redirect()->back();
            /*
            $ret = SysStockOutItemsCart::select('sys_stock_out_items_cart.*','sm_items.part_number AS partno')
            ->join('sm_items','sm_items.id','sys_stock_out_items_cart.part_number')
            ->where('cart_id',session('logged_session_data.cart_id'))->get();
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }*/
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();

            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    public function cart_add(Request $request)
    {
        try {
            DB::table('sys_stock_in_items_cart')->insert(
                [
                    'cart_id' => session('logged_session_data.cart_id'),
                    'part_number' => $request->part_number,
                    'description' => $request->description,
                    'qty' => $request->qty,
                    'unitprice' => $request->unitprice,
                    'value' => $request->value,
                    'serialno' => $request->serialno,
                    'narration' => $request->narration,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );
            $ret = SysStockInItemsCart::select('sys_stock_in_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_stock_in_items_cart.part_number')
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
    public function cart_edit(Request $request)
    {

    }
    public function cart_update(Request $request)
    {
        try {
            DB::table('sys_stock_in_items_cart')->where('id', $request->itm_id)->update([
                'part_number' => $request->part_number,
                'description' => $request->description,
                'qty' => $request->qty,
                'unitprice' => $request->unitprice,
                'value' => $request->value,
                'serialno' => $request->serialno,
                'narration' => $request->narration,
            ]);

            $ret = SysStockInItemsCart::select('sys_stock_in_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_stock_in_items_cart.part_number')
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
    public function cart_delete(Request $request)
    {
        try {
            DB::table('sys_stock_in_items_cart')->where('id', $request->id)->delete();
            $ret = SysStockInItemsCart::select('sys_stock_in_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_stock_in_items_cart.part_number')
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

    public function stock_import(Request $request)
    {
        try {
            $data = DB::table('sys_stock_in_items_import as i')->select('i.*')->where('i.status', 1)->where('i.company_id', session('logged_session_data.company_id'))->get();
            $items = DB::table('sm_items')->select('id', 'part_number')->get();
            $currancy = SysCurrency::select('id', 'code')->get();

            return view('backEnd.inventory.importstockin', compact('data', 'items', 'currancy'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function stock_import_list(Request $request)
    {
        try {
            DB::beginTransaction();
            $selected_file = "";
            if ($request->file('import_file') != "") {
                $file = $request->file('import_file');
                $selected_file = md5($file->getClientOriginalName() . time()) . "-stock." . $file->getClientOriginalExtension();
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
                    'created_by' => Auth::user()->id,
                    'company_id' => session('logged_session_data.company_id'),
                ];
                //}
                //$data2[]=$data;

            }

            foreach (array_chunk($data, 1000) as $dt) {
                DB::table('sys_stock_in_items_import')->insert($dt);
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

    public function stock_import_clear(Request $request)
    {
        try {
            DB::table('sys_stock_in_items_import')->where('company_id', session('logged_session_data.company_id'))->delete();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function stock_import_data(Request $request)
    {
        try {
            DB::beginTransaction();
            $items = DB::table('sm_items')->where('company_id', session('logged_session_data.company_id'))->pluck('part_number');

            $pro = DB::table('sm_items')->select('id', 'part_number')->get();
            $currancy = SysCurrency::select('id', 'code')->get();

            $data = DB::table('sys_stock_in_items_import as i')->select('i.*')->where('i.status', 1)->where('i.company_id', session('logged_session_data.company_id'))
                ->whereNotIn('i.part_number', $items)->get();


            if (count($data) > 0) {

                $curr = $currancy->where('code', $data[0]->currancy)->max('id');

                $ssi = new SysStockIn();
                $ssi->date = date('Y-m-d');
                $ssi->doc_number = SysHelper::get_new_code('sys_stock_in', 'EX', 'doc_number');
                $ssi->remarks = $data[0]->remarks;
                $ssi->currancy = $curr;
                $ssi->status = 1;
                $ssi->company_id = session('logged_session_data.company_id');
                $ssi->created_by = Auth::user()->id;
                $ssi->save();
                $ssi->toArray();

                foreach ($data as $items) {

                    $item = $pro->where('part_number', $items->part_number)->max('id');

                    $sii = new SysStockInItems();
                    $sii->stock_in_id = $ssi->id;
                    $sii->part_number = $item;
                    $sii->description = $items->description;
                    $sii->qty = $items->qty;
                    $sii->unitprice = $items->unitprice;
                    $sii->value = $items->value;
                    $sii->serialno = $items->serialno;
                    $sii->narration = $items->narration;
                    $sii->status = 1;
                    $sii->created_by = Auth::user()->id;
                    $sii->save();

                    $str_arr = explode(",", $items->serialno);
                    foreach ($str_arr as $srl) {
                        $values = array('in_id' => $ssi->id, 'pid' => $item, 'serial_no' => $srl);
                        DB::table('sys_stock_in_serial_no')->insert($values);
                    }

                    $istock = new SysItemStock();
                    $istock->stock_in_id = $ssi->id;
                    $istock->account_id = 0;
                    $istock->partno = $item;
                    $istock->qty_in = $items->qty;
                    $istock->price_in = $items->unitprice;
                    $istock->refno = $ssi->doc_number;
                    $istock->doc_number = $ssi->doc_number;
                    $istock->doc_date = $ssi->date;
                    $istock->deal_id = 0;
                    $istock->slno = $items->serialno;
                    $istock->status = 1;
                    $istock->created_by = Auth::user()->id;
                    $istock->company_id = session('logged_session_data.company_id');
                    $istock->currency_id = $ssi->currency;
                    $istock->save();
                }
            }

            DB::table('sys_stock_in_items_import')->where('company_id', session('logged_session_data.company_id'))->delete();
            DB::commit();


            Toastr::success('Excess Stock (Stock In) Imported Successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function search(Request $request)
    {
        try {
            $companyId = session('logged_session_data.company_id');
            $q = $request->get('query');
            $formattedDate = null;

            if ($q && preg_match('/\d{2}[\/\-]\d{2}[\/\-]\d{4}/', $q)) {
                $normalized = str_replace('/', '-', $q);
                $formattedDate = date('Y-m-d', strtotime($normalized));
            }

            $suppliers = SysStockIn::with([
                'createdby' => function ($q2) {
                    $q2->select('user_id', 'full_name as full_name'); // map to fullname
                }
            ])
                ->where('company_id', $companyId)
                ->where(function ($query) use ($q, $formattedDate) {
                    $query->where('doc_number', 'like', "%{$q}%");

                    if ($formattedDate) {
                        $query->orWhereDate('date', $formattedDate);
                    }

                    // Search by created_by relation (staff name)
                    $query->orWhereHas('createdby', function ($staffQuery) use ($q) {
                        $staffQuery->where('full_name', 'like', "%{$q}%");
                    });
                })
                ->get();

            return response()->json($suppliers);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


}

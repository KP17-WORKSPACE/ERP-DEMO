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
use App\SysStockInSerialNo;
use App\SysStockOut;
use App\SysStockOutItems;
use App\SysStockOutItemsCart;
use App\SysStockOutSerialNo;
use App\SysPurchaseGrnLicenseKey;
use App\SysSupplierType;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;
use Validator;
use PHPExcel;
use PHPExcel_IOFactory;

class SysStockOutController extends Controller
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
            $cart = SysStockOutItemsCart::select('sys_stock_out_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_stock_out_items_cart.part_number')
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
            $data = SysStockOut::where('company_id', session('logged_session_data.company_id'))->get();

            $active_id = $id;

            $selectedStock = null;
            $action = null;
            $editData = null;
            $createData = null;
            $customers = [];
            $suppliers = [];

            if ($request->has('stockout_action')) {
                $StockOUTAction = $request->input('stockout_action');

                if ($StockOUTAction === 'add') {
                    $action = 'add';
                    $customers = SysHelper::get_customer_list(null);
                    $suppliers = SysHelper::get_supplier_list(null);
                    $createData = $this->getCreateData();
                } elseif ($StockOUTAction === 'edit') {
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
            }





            return view('backEnd.inventory.StockOutList', compact('data', 'selectedStock', 'active_id', 'action', 'editData', 'createData', 'customers', 'suppliers'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            //return redirect()->back();
            return $e;
        }
    }

    public function get_print_data($id)
    {
        try {
            $edit = SysStockOut::where('id', $id)->first();
            $company = SysCompany::find($edit->company_id);
            $edit_items = SysStockOutItems::select('sys_stock_out_items.*', 'sm_items.part_number AS partno', 'sm_items.product_type as product_type')
                ->join('sm_items', 'sm_items.id', 'sys_stock_out_items.part_number')
                ->where('stock_out_id', $id)->get();

            return compact('edit', 'edit_items', 'company');
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }
    // public function index(Request $request)
    // {
    //     try{
    //         $r = SysHelper::get_data_by_role();
    //         $company_id = $r[0];
    //         $currency = SysCurrency::select('id','code')->get();
    //         $items = SysHelper::get_product_list($company_id);
    //         $cart = SysStockOutItemsCart::select('sys_stock_out_items_cart.*','sm_items.part_number AS partno')
    //         ->join('sm_items','sm_items.id','sys_stock_out_items_cart.part_number')
    //         ->where('cart_id',session('logged_session_data.cart_id'))->get();
    //         return view('backEnd.inventory.StockOutForm', compact('currency','items','cart'));
    //     }catch (\Exception $e) {
    //         return $e;
    //        Toastr::error('Operation Failed', 'Failed');
    //        return redirect()->back(); 
    //     }
    // }

    public function stockoutgetsrl(Request $request)
    {
        $input = $request->all();

        try {
            $retData = DB::table('sys_stock_in_serial_no')->select('serial_no', 'id')->where(['serial_no' => $request->serial_no, 'status' => 1])->get();
            $bug = 0;
        } catch (\Exception $e) {
            return $e;
            $bug = $e->errorInfo[1];
        }
        if ($bug == 0) {
            return json_encode(array('data' => $retData));
        } else {
            $retData = 'ERROR';
            return json_encode(array('data' => $retData));
        }
    }

    public function store(Request $request)
    {


        DB::beginTransaction();
        try {

            // $cart = SysStockOutItemsCart::select('sys_stock_out_items_cart.*', 'sm_items.part_number AS partno')
            //     ->join('sm_items', 'sm_items.id', 'sys_stock_out_items_cart.part_number')
            //     ->where('cart_id', session('logged_session_data.cart_id'))->get();
            // if (count($cart) > 0) {

            // } else {
            //     Toastr::error('Items not found', 'Failed');
            //     return redirect()->back();
            // }

            $ssi = new SysStockOut();
            $ssi->date = SysHelper::normalizeToYmd($request->date);

            if ($request->mode == "SH") { //mode 1 cash, mode 2 bank
                $ssi->doc_number = SysHelper::get_new_code('sys_stock_out', 'SH', 'doc_number');

            } elseif ($request->mode == "DO") {
                $ssi->doc_number = SysHelper::get_new_code('sys_stock_out', 'DO', 'doc_number');
            } else {
                $ssi->doc_number = SysHelper::get_new_code('sys_stock_out', 'RO', 'doc_number');
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
            $ssi->save();
            $ssi->toArray();




            for ($i = 0; $i < count($request->part_number); $i++) {
                if ($request->qty[$i] != "" && $request->qty[$i] > 0) {
                    $sii = new SysStockOutItems();
                    $sii->stock_out_id = $ssi->id;
                    $sii->part_number = $request->part_number[$i] ?? null;
                    $sii->description = $request->description[$i] ?? null;
                    $sii->qty = str_replace(',', '', $request->qty[$i]);
                    $sii->unitprice = str_replace(',', '', $request->unitprice[$i] ?? 0);
                    $sii->value = str_replace(',', '', $request->value[$i] ?? 0);
                    
                    $sii->serialno = $request->serial_no[$i] ?? '';
                    $sii->narration = $request->narration[$i] ?? '';
                    $sii->status = 1;
                    $sii->created_by = Auth::user()->id;
                    $sii->save();

                    $str_arr = explode(",", $request->serial_no[$i]);
                    foreach ($str_arr as $srl) {
                        $values = array('out_id' => $ssi->id, 'pid' => $request->part_number[$i], 'serial_no' => $srl);
                        DB::table('sys_stock_out_serial_no')->insert($values);
                    }

                    $istock = new SysItemStock();
                    $istock->stock_out_id = $ssi->id;
                    $istock->account_id = 0;
                    $istock->partno = $request->part_number[$i];
                    $istock->qty_out = str_replace(',', '', $request->qty[$i]);
                    $istock->price_out = str_replace(',', '', $request->unitprice[$i] ?? 0);
                    $istock->refno = $ssi->doc_number;
                    $istock->doc_number = $ssi->doc_number;
                    $istock->doc_date = $ssi->date;
                    $istock->deal_id = 0;
                    $istock->slno = $request->serial_no[$i] ?? '';
                    $istock->status = 1;
                    if ($request->mode == "RO")
                        $istock->description = $customer_name . " " . $supplier_name;
                    $istock->created_by = Auth::user()->id;
                    $istock->company_id = session('logged_session_data.company_id');
                    $istock->currency_id = $ssi->currency;
                    $istock->save();

                    $key_item = SysPurchaseGrnLicenseKey::where('item_id', $request->part_number[$i])
                        ->where('stock_out_id', -1)
                        ->where('cart_id', session('logged_session_data.cart_id'))
                        ->where('company_id', session('logged_session_data.company_id'))
                        ->get();

                    if (count($key_item) > 0) {
                        foreach ($key_item as $k) {
                            SysHelper::set_license_key_trn(6, $ssi->id, $ssi->date, $ssi->doc_number, $k->id, $k->item_id, $k->license_key, $k->exp_date);
                            SysPurchaseGrnLicenseKey::where('item_id', $request->part_number[$i])
                                ->where('license_key', $k->license_key)
                                ->where('status', 1)
                                ->where('stock_out_id', -1)
                                ->where('company_id', session('logged_session_data.company_id'))
                                ->update([
                                    'status' => 2,
                                    'stock_out_id' => $ssi->id,
                                    'updated_by' => Auth::user()->id,
                                    'updated_at' => Carbon::now('+04:00')
                                ]);
                        }
                    }
                }
            }

            SysStockOutItemsCart::where('cart_id', session('logged_session_data.cart_id'))->delete();
            $results = 0;
            DB::commit();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($results == 0) {
                    return ApiBaseMethod::sendResponse(null, 'Stock Out has been added successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } else {
                if ($results == 0) {
                    Toastr::success('Stock Out has been added successfully', 'Success');
                    return redirect('stock-out/'.$ssi->id);
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
    public function view($id)
    {
        try {


            $edit = SysStockOut::where('id', $id)->first();
            $company = SysCompany::find($edit->company_id);

            $edit_items = SysStockOutItems::select('sys_stock_out_items.*', 'sm_items.part_number AS partno', 'sm_items.product_type as product_type')
                ->join('sm_items', 'sm_items.id', 'sys_stock_out_items.part_number')
                ->where('stock_out_id', $id)->get();
            return view('backEnd.inventory.StockOutFormView', compact('company', 'edit', 'edit_items'));
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
            $customers = SysHelper::get_customer_list(null);
            $suppliers = SysHelper::get_supplier_list(null);

            $mode = null;

            $items = SysHelper::get_product_list($company_id);
            $edit = SysStockOut::where('id', $id)->first();

            // Take first 2 letters
            $prefix = substr($edit->doc_number, 0, 2);

            switch ($prefix) {
                case 'RO':
                    $mode = 'RO';
                    break;
                case 'DO':
                    $mode = 'DO';
                    break;
                case 'SH':
                    $mode = 'SH';
                    break;
                default:
                    $mode = 'Unknown';
            }

            $edit_items = SysStockOutItems::select('sys_stock_out_items.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_stock_out_items.part_number')
                ->where('stock_out_id', $id)->get();
            return compact('currency', 'items', 'edit', 'edit_items', 'mode', 'suppliers', 'customers');
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function items_add(Request $request)
    {
        try {
            DB::table('sys_stock_out_items')->insert(
                [
                    'stock_out_id' => $request->stock_out_id,
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
            $ret = SysStockOutItems::select('sys_stock_out_items.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_stock_out_items.part_number')
                ->where('stock_out_id', $request->stock_out_id)->get();
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
            DB::table('sys_stock_out_items')->where('id', $request->itm_id)->update([
                'part_number' => $request->part_number,
                'description' => $request->description,
                'qty' => str_replace(',', '', $request->qty),
                'unitprice' =>  str_replace(',', '', $request->unitprice),
                'value' => str_replace(',', '', $request->value),
                'serialno' => $request->serialno,
                'narration' => $request->narration,
            ]);
            $ret = SysStockOutItems::select('sys_stock_out_items.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_stock_out_items.part_number')
                ->where('stock_out_id', $request->stock_out_id)->get();

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
            DB::table('sys_stock_out_items')->where('id', $request->id)->delete();
            $ret = SysStockOutItems::select('sys_stock_out_items.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_stock_out_items.part_number')
                ->where('stock_out_id', $request->stock_out_id)->get();

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

            // $cart = SysStockOutItems::select('sys_stock_out_items.*', 'sm_items.part_number AS partno')
            //     ->join('sm_items', 'sm_items.id', 'sys_stock_out_items.part_number')
            //     ->where('stock_out_id', $id)->get();
            // if (count($cart) > 0) {

            // } else {
            //     Toastr::error('Items not found', 'Failed');
            //     return redirect()->back();
            // }

            $ssi = SysStockOut::find($id);

            $customer_name = "";
            $supplier_name = "";
            $mode_change_remarks = "";

            if ($request->mode != $request->current_mode) {
                if ($request->mode == "SH") { //mode 1 cash, mode 2 bank
                    $doc = SysHelper::get_new_code('sys_stock_out', 'SH', 'doc_number');
                } elseif ($request->mode == "DO") {
                    $doc = SysHelper::get_new_code('sys_stock_out', 'DO', 'doc_number');
                } else {
                    $doc = SysHelper::get_new_code('sys_stock_out', 'RO', 'doc_number');
                }

                $mode_change_remarks = $ssi->doc_number . " change to " . $doc;
                $ssi->doc_number = $doc;
            }

            if ($request->mode == "RO") {

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


            $ssi->date = SysHelper::normalizeToYmd($request->date);
            if ($mode_change_remarks != "") {
                $ssi->remarks = $mode_change_remarks . " / " . $request->remarks;
            } else {
                $ssi->remarks = $request->remarks;
            }
            // $ssi->remarks = $request->remarks;
            $ssi->currancy = $request->currency;
            $ssi->status = 1;
            $ssi->updated_by = Auth::user()->id;
            $ssi->updated_at = Carbon::now('+04:00');
            $ssi->save();
            $ssi->toArray();

            DB::table('sys_stock_out_serial_no')->where('out_id', $ssi->id)->delete();
            DB::table('sys_item_stock')->where('stock_out_id', $ssi->id)->delete();
            DB::table('sys_stock_out_items')->where('stock_out_id', $ssi->id)->delete();

            for ($i = 0; $i < count($request->part_number); $i++) {
                if ($request->qty[$i] != "" && $request->qty[$i] > 0) {
                    $sii = new SysStockOutItems();
                    $sii->stock_out_id = $ssi->id;
                    $sii->part_number = $request->part_number[$i] ?? null;
                    $sii->description = $request->description[$i] ?? null;
                    $sii->qty = str_replace(',', '', $request->qty[$i]);
                    $sii->unitprice = str_replace(',', '', $request->unitprice[$i] ?? 0);
                    $sii->value = str_replace(',', '', $request->value[$i] ?? 0);
                    $sii->serialno = $request->serial_no[$i] ?? '';
                    $sii->narration = $request->narration[$i] ?? '';
                    $sii->status = 1;
                    $sii->created_by = Auth::user()->id;
                    $sii->save();

                    $str_arr = explode(",", $request->serial_no[$i]);
                    foreach ($str_arr as $srl) {
                        $values = array('out_id' => $ssi->id, 'pid' => $request->part_number[$i], 'serial_no' => $srl);
                        DB::table('sys_stock_out_serial_no')->insert($values);
                    }

                    $istock = new SysItemStock();
                    $istock->stock_out_id = $ssi->id;
                    $istock->account_id = 0;
                    $istock->partno = $request->part_number[$i];
                    $istock->qty_out = str_replace(',', '', $request->qty[$i]);
                    $istock->price_out = str_replace(',', '', $request->unitprice[$i] ?? 0);
                    $istock->refno = $ssi->doc_number;
                    $istock->doc_number = $ssi->doc_number;
                    $istock->doc_date = $ssi->date;
                    $istock->deal_id = 0;
                    $istock->slno = $request->serial_no[$i] ?? '';
                    $istock->status = 1;
                    if ($request->mode == "RO")
                        $istock->description = $customer_name . " " . $supplier_name;
                    $istock->created_by = Auth::user()->id;
                    $istock->company_id = session('logged_session_data.company_id');
                    $istock->currency_id = $ssi->currency;
                    $istock->save();

                    $key_item = SysPurchaseGrnLicenseKey::where('item_id', $request->part_number[$i])
                        ->where('stock_out_id', -1)
                        ->where('cart_id', session('logged_session_data.cart_id'))
                        ->where('company_id', session('logged_session_data.company_id'))
                        ->get();

                    if (count($key_item) > 0) {
                        foreach ($key_item as $k) {
                            SysHelper::set_license_key_trn(6, $ssi->id, $ssi->date, $ssi->doc_number, $k->id, $k->item_id, $k->license_key, $k->exp_date);
                            SysPurchaseGrnLicenseKey::where('item_id', $request->part_number[$i])
                                ->where('license_key', $k->license_key)
                                ->where('status', 1)
                                ->where('stock_out_id', -1)
                                ->where('company_id', session('logged_session_data.company_id'))
                                ->update([
                                    'status' => 2,
                                    'stock_out_id' => $ssi->id,
                                    'updated_by' => Auth::user()->id,
                                    'updated_at' => Carbon::now('+04:00')
                                ]);
                        }
                    }
                }
            }


            // SysStockOutItemsCart::where('cart_id', session('logged_session_data.cart_id'))->delete();
            $results = 0;
            DB::commit();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($results == 0) {
                    return ApiBaseMethod::sendResponse(null, 'Stock Out has been updated successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } else {
                if ($results == 0) {
                    Toastr::success('Stock Out has been updated successfully', 'Success');
                    return redirect('stock-out/'.$ssi->id);
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

    public function destroy($id)
    {
        //
    }

    public function deleteStoreView(Request $request, $id)
    {

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($id, null);
        }
        return view('backEnd.inventory.deleteItemStoreView', compact('id'));
    }

    public function deleteStore(Request $request, $id)
    {
        $result = SmItemStore::destroy($id);

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($result) {
                return ApiBaseMethod::sendResponse(null, 'Store  has been deleted successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again');
            }
        } else {
            if ($result) {
                return redirect('item-store')->with('message-success-delete', 'Store  has been deleted successfully');
            } else {
                return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
            }
        }
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

                        DB::table('sys_stock_out_items_cart')->insert(
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
            DB::table('sys_stock_out_items_cart')->insert(
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
            $ret = SysStockOutItemsCart::select('sys_stock_out_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_stock_out_items_cart.part_number')
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
            DB::table('sys_stock_out_items_cart')->where('id', $request->itm_id)->update([
                'part_number' => $request->part_number,
                'description' => $request->description,
                'qty' => $request->qty,
                'unitprice' => $request->unitprice,
                'value' => $request->value,
                'serialno' => $request->serialno,
                'narration' => $request->narration,
            ]);

            $ret = SysStockOutItemsCart::select('sys_stock_out_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_stock_out_items_cart.part_number')
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
            DB::table('sys_stock_out_items_cart')->where('id', $request->id)->delete();
            $ret = SysStockOutItemsCart::select('sys_stock_out_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_stock_out_items_cart.part_number')
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
            $data = DB::table('sys_stock_out_items_import as i')->select('i.*')->where('i.status', 1)->where('i.company_id', session('logged_session_data.company_id'))->get();
            $items = DB::table('sm_items')->select('id', 'part_number')->get();
            $currancy = SysCurrency::select('id', 'code')->get();

            return view('backEnd.inventory.importstockout', compact('data', 'items', 'currancy'));
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
                DB::table('sys_stock_out_items_import')->insert($dt);
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
            DB::table('sys_stock_out_items_import')->where('company_id', session('logged_session_data.company_id'))->delete();
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

            $data = DB::table('sys_stock_out_items_import as i')->select('i.*')->where('i.status', 1)->where('i.company_id', session('logged_session_data.company_id'))
                ->whereNotIn('i.part_number', $items)->get();


            if (count($data) > 0) {

                $curr = $currancy->where('code', $data[0]->currancy)->max('id');

                $ssi = new SysStockOut();
                $ssi->date = date('Y-m-d');
                $ssi->doc_number = SysHelper::get_new_code('sys_stock_out', 'SH', 'doc_number');
                $ssi->remarks = $data[0]->remarks;
                $ssi->currancy = $curr;
                $ssi->status = 1;
                $ssi->company_id = session('logged_session_data.company_id');
                $ssi->created_by = Auth::user()->id;
                $ssi->save();
                $ssi->toArray();

                foreach ($data as $items) {

                    $item = $pro->where('part_number', $items->part_number)->max('id');

                    $sii = new SysStockOutItems();
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
                        $values = array('out_id' => $ssi->id, 'pid' => $item, 'serial_no' => $srl);
                        DB::table('sys_stock_out_serial_no')->insert($values);
                    }

                    $istock = new SysItemStock();
                    $istock->stock_out_id = $ssi->id;
                    $istock->account_id = 0;
                    $istock->partno = $item;
                    $istock->qty_out = $items->qty;
                    $istock->price_out = $items->unitprice;
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

            DB::table('sys_stock_out_items_import')->where('company_id', session('logged_session_data.company_id'))->delete();
            DB::commit();


            Toastr::success('Shortage Stock (Stock Out) Imported Successfully', 'Success');
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

            $suppliers = SysStockOut::with([
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
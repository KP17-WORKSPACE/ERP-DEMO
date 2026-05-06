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
use App\SysCustSupplAddressbook;
use App\SysHelper;
use App\SysItemOpeningStock;
use App\SysItemStock;
use App\SysPackingList;
use App\SysPackingListItems;
use App\SysPackingListItemsCart;
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
use Barryvdh\DomPDF\Facade as PDF;

class SysPackingListController extends Controller
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
            $account = SysHelper::get_customer_supplier_list_all($company_id);
            $cart = SysPackingListItemsCart::select('sys_packing_list_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_packing_list_items_cart.part_number')
                ->where('cart_id', session('logged_session_data.cart_id'))->get();
            return compact('currency', 'items', 'cart', 'account');

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function index(Request $request, $id = null)
    {
        try {
            $data = SysPackingList::where('company_id', session('logged_session_data.company_id'))->get();
            $active_id = $id;

            $selectedPack = null;
            $action = null;
            $editData = null;
            $createData = null;



            if ($request->has('packing_action')) {
                $PackINAction = $request->input('packing_action');

                if ($PackINAction === 'add') {
                    $action = 'add';

                    $createData = $this->getCreateData();
                } elseif ($PackINAction === 'edit') {
                    $action = 'edit';
                    $editData = $this->edit($active_id);

                }
            } else {

                if ($id) {
                    $selectedPack = $this->get_print_data($id);
                } else {
                    $firstRecord = $data->first();
                    if ($firstRecord) {
                        $active_id = $firstRecord->id;
                        $selectedPack = $this->get_print_data($firstRecord->id);
                    }
                }

            }
            return view('backEnd.inventory.PackingList', compact('data', 'selectedPack', 'action', 'editData', 'createData', 'active_id'));
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
    //         $account = SysHelper::get_customer_supplier_list_all($company_id);
    //         $cart = SysPackingListItemsCart::select('sys_packing_list_items_cart.*','sm_items.part_number AS partno')
    //         ->join('sm_items','sm_items.id','sys_packing_list_items_cart.part_number')
    //         ->where('cart_id',session('logged_session_data.cart_id'))->get();
    //         return view('backEnd.inventory.PackingListForm', compact('currency','items','cart','account'));
    //     }catch (\Exception $e) {
    //         return $e;
    //        Toastr::error('Operation Failed', 'Failed');
    //        return redirect()->back(); 
    //     }
    // }


    public function get_print_data($id)
    {
        try {

            $address = "";
            $address2 = "";
            $city = "";
            $state = "";
            $country = "";
            $contact_name = "";
            $email = "";
            $tel = "";
            $ship_company_name = "";
            $ship_address1 = "";
            $ship_address2 = "";
            $ship_contact_name = "";
            $ship_tel = "";
            $ship_email = "";
            $delivery_city = "";
            $delivery_zip_code = "";
            $delivery_country = "";
            $delivery_state = "";

            $pk = SysPackingList::find($id);
            if (!empty($pk)) {
                $company = SysCompany::find($pk->company_id);
                $items = SysPackingListItems::where('packing_list_id', '=', $pk->id)->get();
                $sup_email = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_chartofaccounts.id', $pk->account_id)->first();
                if (!empty($sup_email)) {
                    $add = SysCustSupplAddressbook::where('cust_suppl_id', $sup_email->id)->first();
                }

                $contact_name = $sup_email->customer_salutation . ' ' . $sup_email->first_name . ' ' . $sup_email->last_name;
                $email = $sup_email->email;
                $tel = $sup_email->contcat_number;

                if (!empty($add)) {
                    $address = $add->address;
                    $address2 = $add->address2;
                    $city = $add->city;
                    $state = $add->statename->name;
                    $country = $add->countryname->name;
                }


                $data = [
                    'pk' => $pk,
                    'company' => $company,
                    'items' => $items,

                    'email' => $email,
                    'tel' => $tel,
                    'address' => $address,
                    'address2' => $address2,
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                    'contact_name' => $contact_name,

                    'ship_company_name' => $ship_company_name,
                    'ship_address1' => $ship_address1,
                    'ship_address2' => $ship_address2,
                    'delivery_city' => $delivery_city,
                    'delivery_zip_code' => $delivery_zip_code,
                    'delivery_country' => $delivery_country,
                    'delivery_state' => $delivery_state,

                    'ship_contact_name' => $ship_contact_name,
                    'ship_tel' => $ship_tel,
                    'ship_email' => $ship_email,

                    // 'email' => $email,
                    // 'tel' => $tel,
                    // 'address' => $address,
                    // 'address2' => $address2,
                    // 'city' => $city,
                    // 'state' => $state,
                    // 'country' => $country,
                ];
                //return view('backEnd.pdf_print.pk_pdf', $data);


                return $data;
            } else {
                return "error!!";
            }
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

            // $cart = SysPackingListItemsCart::select('sys_packing_list_items_cart.*','sm_items.part_number AS partno')
            // ->join('sm_items','sm_items.id','sys_packing_list_items_cart.part_number')
            // ->where('cart_id',session('logged_session_data.cart_id'))->get();
            // if(count($cart)>0) {

            // } else {
            //     Toastr::error('Items not found', 'Failed');
            //     return redirect()->back();
            // }


            $ssi = new SysPackingList();
            $ssi->account_id = $request->account_id;
            $ssi->date = Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d');
            $ssi->doc_number = SysHelper::get_new_code('sys_packing_list', 'PK', 'doc_number');
            $ssi->refdate = Carbon::createFromFormat('d/m/Y', $request->refdate)->format('Y-m-d');
            $ssi->refno = $request->refno;
            $ssi->remarks = $request->remarks;
            $ssi->currancy = $request->currency;
            $ssi->status = 1;
            $ssi->company_id = session('logged_session_data.company_id');
            $ssi->created_by = Auth::user()->id;
            $ssi->save();
            $ssi->toArray();

            for ($i = 0; $i < count($request->part_number); $i++) {
                if ($request->qty[$i] != "" && $request->qty[$i] > 0) {
                    $sii = new SysPackingListItems();
                    $sii->packing_list_id = $ssi->id;
                    $sii->boxno = $request->box_no[$i];
                    $sii->part_number = $request->part_number[$i];
                    $sii->qty = $request->qty[$i];
                    $sii->coo = $request->coo[$i];
                    $sii->hscode = $request->hscode[$i];
                    $sii->weight = $request->weight[$i];
                    $sii->dimension = $request->dimension[$i];
                    $sii->status = 1;
                    $sii->created_by = Auth::user()->id;
                    $sii->save();
                }
            }


            SysPackingListItemsCart::where('cart_id', session('logged_session_data.cart_id'))->delete();
            $results = 0;
            DB::commit();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($results == 0) {
                    return ApiBaseMethod::sendResponse(null, 'Packing List has been added successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } else {
                if ($results == 0) {
                    Toastr::success('Packing List has been added successfully', 'Success');
                    return redirect('packing-list/'.$ssi->id);
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

    public function download(Request $request, $id)
    {
        try {

            $address = "";
            $address2 = "";
            $city = "";
            $state = "";
            $country = "";
            $contact_name = "";
            $email = "";
            $tel = "";
            $ship_company_name = "";
            $ship_address1 = "";
            $ship_address2 = "";
            $ship_contact_name = "";
            $ship_tel = "";
            $ship_email = "";
            $delivery_city = "";
            $delivery_zip_code = "";
            $delivery_country = "";
            $delivery_state = "";

            $pk = SysPackingList::find($id);
            if (!empty($pk)) {
                $company = SysCompany::find($pk->company_id);
                $items = SysPackingListItems::where('packing_list_id', '=', $pk->id)->get();
                $sup_email = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_chartofaccounts.id', $pk->account_id)->first();
                if (!empty($sup_email)) {
                    $add = SysCustSupplAddressbook::where('cust_suppl_id', $sup_email->id)->first();
                }

                $contact_name = $sup_email->customer_salutation . ' ' . $sup_email->first_name . ' ' . $sup_email->last_name;
                $email = $sup_email->email;
                $tel = $sup_email->contcat_number;

                if (!empty($add)) {
                    $address = $add->address;
                    $address2 = $add->address2;
                    $city = $add->city;
                    $state = $add->statename->name;
                    $country = $add->countryname->name;
                }


                $data = [
                    'pk' => $pk,
                    'company' => $company,
                    'items' => $items,

                    'email' => $email,
                    'tel' => $tel,
                    'address' => $address,
                    'address2' => $address2,
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                    'contact_name' => $contact_name,

                    'ship_company_name' => $ship_company_name,
                    'ship_address1' => $ship_address1,
                    'ship_address2' => $ship_address2,
                    'delivery_city' => $delivery_city,
                    'delivery_zip_code' => $delivery_zip_code,
                    'delivery_country' => $delivery_country,
                    'delivery_state' => $delivery_state,

                    'ship_contact_name' => $ship_contact_name,
                    'ship_tel' => $ship_tel,
                    'ship_email' => $ship_email,

                    // 'email' => $email,
                    // 'tel' => $tel,
                    // 'address' => $address,
                    // 'address2' => $address2,
                    // 'city' => $city,
                    // 'state' => $state,
                    // 'country' => $country,
                ];
                //return view('backEnd.pdf_print.pk_pdf', $data);
                $pdf = PDF::loadView('backEnd.pdf_print.pk_pdf', $data);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download($pk->doc_number . '-' . $pk->account->account_name . ".pdf");
            } else {
                return "error!!";
            }
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
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
            $data = $this->get_print_data($id);



            return view('backEnd.inventory.PackingListFormView', $data);
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
            $account = SysHelper::get_customer_supplier_list_all($company_id);
            $edit = SysPackingList::where('id', $id)->first();
            $edit_items = SysPackingListItems::select('sys_packing_list_items.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_packing_list_items.part_number')
                ->where('packing_list_id', $id)->get();
            return compact('currency', 'items', 'edit', 'edit_items', 'account');
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function items_add(Request $request)
    {
        try {
            DB::table('sys_packing_list_items')->insert(
                [
                    'packing_list_id' => $request->packing_list_id,
                    'boxno' => $request->boxno,
                    'part_number' => $request->part_number,
                    'qty' => $request->qty,
                    'coo' => $request->coo,
                    'hscode' => $request->hscode,
                    'weight' => $request->weight,
                    'dimension' => $request->dimension,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );
            $ret = SysPackingListItems::select('sys_packing_list_items.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_packing_list_items.part_number')
                ->where('packing_list_id', $request->packing_list_id)->get();
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
            DB::table('sys_packing_list_items')->where('id', $request->itm_id)->update([
                'part_number' => $request->part_number,
                'boxno' => $request->boxno,
                'qty' => $request->qty,
                'coo' => $request->coo,
                'hscode' => $request->hscode,
                'weight' => $request->weight,
                'dimension' => $request->dimension,
            ]);

            $ret = SysPackingListItems::select('sys_packing_list_items.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_packing_list_items.part_number')
                ->where('packing_list_id', $request->packing_list_id)->get();

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
            DB::table('sys_packing_list_items')->where('id', $request->id)->delete();
            $ret = SysPackingListItems::select('sys_packing_list_items.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_packing_list_items.part_number')
                ->where('packing_list_id', $request->packing_list_id)->get();

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

            // $cart = SysPackingListItems::select('sys_packing_list_items.*', 'sm_items.part_number AS partno')
            //     ->join('sm_items', 'sm_items.id', 'sys_packing_list_items.part_number')
            //     ->where('packing_list_id', $id)->get();
            // if (count($cart) > 0) {

            // } else {
            //     Toastr::error('Items not found', 'Failed');
            //     return redirect()->back();
            // }

            $ssi = SysPackingList::find($id);
            $ssi->account_id = $request->account_id;
            $ssi->date = Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d');
            $ssi->refdate = Carbon::createFromFormat('d/m/Y', $request->refdate)->format('Y-m-d');
            $ssi->refno = $request->refno;
            $ssi->remarks = $request->remarks;
            $ssi->currancy = $request->currency;
            $ssi->status = 1;
            $ssi->updated_by = Auth::user()->id;
            $ssi->updated_at = Carbon::now('+04:00');
            $ssi->save();
            $ssi->toArray();

            DB::table('sys_packing_list_items')->where('packing_list_id', $ssi->id)->delete();

            for ($i = 0; $i < count($request->part_number); $i++) {
                if ($request->qty[$i] != "" && $request->qty[$i] > 0) {
                    $sii = new SysPackingListItems();
                    $sii->packing_list_id = $ssi->id;
                    $sii->boxno = $request->box_no[$i];
                    $sii->part_number = $request->part_number[$i];
                    $sii->qty = $request->qty[$i];
                    $sii->coo = $request->coo[$i];
                    $sii->hscode = $request->hscode[$i];
                    $sii->weight = $request->weight[$i];
                    $sii->dimension = $request->dimension[$i];
                    $sii->status = 1;
                    $sii->created_by = Auth::user()->id;
                    $sii->save();
                }
            }

            SysPackingListItemsCart::where('cart_id', session('logged_session_data.cart_id'))->delete();
            $results = 0;
            DB::commit();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($results == 0) {
                    return ApiBaseMethod::sendResponse(null, 'Packing List has been updated successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } else {
                if ($results == 0) {
                    Toastr::success('Packing List has been updated successfully', 'Success');
                    return redirect('packing-list/'.$ssi->id);
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
    public function cart_add(Request $request)
    {
        try {
            DB::table('sys_packing_list_items_cart')->insert(
                [
                    'cart_id' => session('logged_session_data.cart_id'),
                    'boxno' => $request->boxno,
                    'part_number' => $request->part_number,
                    'qty' => $request->qty,
                    'coo' => $request->coo,
                    'hscode' => $request->hscode,
                    'weight' => $request->weight,
                    'dimension' => $request->dimension,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );
            $ret = SysPackingListItemsCart::select('sys_packing_list_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_packing_list_items_cart.part_number')
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

    public function cart_add_excel(Request $request)
    {
        try {
            if (!isset($request->part_number) || !is_array($request->part_number) || count($request->part_number) == 0) {
                return response()->json(['success' => false, 'message' => 'No data found to import']);
            }

            DB::beginTransaction();

            $insertedIds = [];

            foreach ($request->part_number as $index => $part_number_value) {
                if (!$part_number_value) {
                    continue;
                }

                $item = SmItem::where('status', 1)
                    ->where(function ($query) use ($part_number_value) {
                        $query->where('part_number', $part_number_value);
                        if (is_numeric($part_number_value)) {
                            $query->orWhere('id', $part_number_value);
                        }
                    })
                    ->first();

                $part_id = $item ? $item->id : null;

                $coo = $request->coo[$index] ?? '';
                $hscode = $request->hscode[$index] ?? '';
                $weight = $request->weight[$index] ?? '';

                if ($item) {
                    if (empty($coo) && !empty($item->coo)) $coo = $item->coo;
                    if (empty($hscode) && !empty($item->hscode)) $hscode = $item->hscode;
                    if (empty($weight) && !empty($item->weight)) $weight = $item->weight;
                }

                $id = DB::table('sys_packing_list_items_cart')->insertGetId([
                    'cart_id' => session('logged_session_data.cart_id'),
                    'boxno' => $request->box_no[$index] ?? '',
                    'part_number' => $part_id,
                    'qty' => $request->qty[$index] ?? 0,
                    'coo' => $coo,
                    'hscode' => $hscode,
                    'weight' => $weight,
                    'dimension' => $request->dimension[$index] ?? '',
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]);

                $insertedIds[] = $id;
            }

            $ret = SysPackingListItemsCart::select('sys_packing_list_items_cart.*', 'sm_items.part_number AS partno')
                ->leftJoin('sm_items', 'sm_items.id', 'sys_packing_list_items_cart.part_number')
                ->whereIn('sys_packing_list_items_cart.id', $insertedIds)
                ->get();

            DB::commit();

            return response()->json(['success' => true, 'data' => $ret]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function cart_edit(Request $request)
    {

    }
    public function cart_update(Request $request)
    {
        try {
            DB::table('sys_packing_list_items_cart')->where('id', $request->itm_id)->update([
                'part_number' => $request->part_number,
                'boxno' => $request->boxno,
                'qty' => $request->qty,
                'coo' => $request->coo,
                'hscode' => $request->hscode,
                'weight' => $request->weight,
                'dimension' => $request->dimension,
            ]);

            $ret = SysPackingListItemsCart::select('sys_packing_list_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_packing_list_items_cart.part_number')
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
            DB::table('sys_packing_list_items_cart')->where('id', $request->id)->delete();
            $ret = SysPackingListItemsCart::select('sys_packing_list_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_packing_list_items_cart.part_number')
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

            $suppliers = SysPackingList::with([
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

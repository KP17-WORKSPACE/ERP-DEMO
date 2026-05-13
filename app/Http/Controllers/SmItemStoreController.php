<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SmInspectingDepartment;
use App\SmItem;
use App\SmItemsCart;
use Illuminate\Http\Request;
use App\SmItemStore;
use App\SmStaff;
use App\SysBrand;
use App\SysChartofAccounts;
use App\SysChartofAccountsTransaction;
use App\SysCompany;
use App\SysCrmQuoteItems;
use App\SysCurrencySettings;
use App\SysHelper;
use App\SysItemOpeningStock;
use App\SysItemStock;
use App\SysItemStockImport;
use App\SysPaymentTerms;
use App\SysPurchaseGrnLicenseKey;
use App\SysShipping;
use App\SysSupplierType;
use App\ReserveStock;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\Return_;
use Illuminate\Support\Facades\Validator;
use PHPExcel;
use PHPExcel_IOFactory;
use App\SysProductType;
use App\SysCustSuppl;

class SmItemStoreController extends Controller
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
    public function index(Request $request)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));

            $items = SysHelper::get_product_list($company_id);

            $cart = DB::table('sys_stock_items_cart')->select('sys_stock_items_cart.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_stock_items_cart.part_number')
                //->where('sm_items.company_id',session('logged_session_data.company_id'))
                ->where('cart_id', session('logged_session_data.cart_id'))->get();

            return view('backEnd.inventory.itemStoreForm', compact('currency', 'items', 'company', 'cart'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }


        $itemstores = SmItemStore::all();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($itemstores, null);
        }
        return view('backEnd.inventory.itemStoreForm', compact('itemstores'));


    }

    function addstockitemscart(Request $request)
    {
        try {
            DB::table('sys_stock_items_cart')->insert(
                [
                    'cart_id' => session('logged_session_data.cart_id'),
                    'part_number' => $request->part_number,
                    'part_number_txt' => $request->part_number_val,
                    'description' => $request->description,
                    'qty' => $request->qty,
                    'unitprice' => $request->unitprice,
                    'value' => $request->value,
                    'refno' => $request->refno,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                    'company_id' => session('logged_session_data.company_id'),
                ]
            );
            $ret = DB::table('sys_stock_items_cart')->select('sys_stock_items_cart.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_stock_items_cart.part_number')
                //->where('sm_items.company_id',session('logged_session_data.company_id'))
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
    function deletestockitemscart(Request $request)
    {
        try {
            DB::table('sys_stock_items_cart')->where([
                'cart_id' => session('logged_session_data.cart_id'),
                'id' => $request->id,
            ])->delete();

            $ret = DB::table('sys_stock_items_cart')->select('sys_stock_items_cart.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_stock_items_cart.part_number')
                //->where('sm_items.company_id',session('logged_session_data.company_id'))
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



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {

    //     $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');

    //     $cart = DB::table('sys_stock_items_cart')->where('cart_id',session('logged_session_data.cart_id'))->where('status',1)->get();
    //     if(count($cart)==0){

    //         Toastr::error('No Item Found. Please Add and Try Again', 'Failed');
    //         return redirect()->back();
    //     }

    //     //$openingstock_account_id = SysChartofAccounts::select('id')->where('account_name','Opening Stock')->first();        
    //     $openingstock_account_id = SysHelper::get_opening_stock_account_id();
    //     if($openingstock_account_id==""){
    //         Toastr::error('Opening Stock Account Not Found. Please Create and Try Again', 'Failed');
    //         return redirect()->back();
    //     }
    //     $account_id = $openingstock_account_id;
    //     DB::beginTransaction();
    //     try {
    //        $ios = new SysItemOpeningStock();
    //        $ios->doc_number = SysHelper::get_new_code('sys_item_opening_stock','OP','doc_number');
    //        $ios->doc_date = date('Y-m-d', strtotime($request->doc_date));
    //        $ios->bill_date = date('Y-m-d', strtotime($request->bill_date));
    //        $ios->currency = $request->currency;
    //        $ios->narration = $request->narration;
    //        $ios->status = 1;
    //        $ios->company_id = session('logged_session_data.company_id');
    //        $ios->created_by = Auth::user()->id;
    //        $ios->created_at = $trn_time;
    //        $ios->save();
    //        $ios->toArray();

    //        if(count($cart)>0){
    //         foreach($cart as $itm){
    //             $ItemStockData[]=[
    //                 'doc_number' => $ios->doc_number,
    //                 'doc_date' => date('Y-m-d', strtotime($request->doc_date)),
    //                 'ops_id' => $ios->id,
    //                 'account_id' => $account_id,
    //                 'partno' => $itm->part_number,
    //                 'description' => $itm->description,
    //                 'slno' => "",
    //                 'qty_in' => $itm->qty,
    //                 'price_in' => $itm->unitprice,
    //                 'refno' => $itm->refno,
    //                 'remarks' => "",
    //                 'status' => 1,
    //                 'created_by' => Auth::user()->id,
    //                 'created_at' => $trn_time,
    //                 'company_id' => session('logged_session_data.company_id'),
    //                 'currency_id' => $request->currency,
    //             ];
    //         }
    //        }
    //        SysItemStock::insert($ItemStockData);
    //        $amount_dr=$cart->sum('value');
    //        $amount_cr='0.00';
    //        SysHelper::trn_chartof_accounts_transaction($account_id,$ios->id,$ios->doc_number,$ios->doc_date,'openingstock',$amount_dr,$amount_cr,$request->narration,1,0,"",0);

    //        DB::table('sys_stock_items_cart')->where('cart_id', session('logged_session_data.cart_id'))->delete();

    //     //    for($i = 0; $i < count($request->part_number); $i++) {
    //     //        if($request->part_number[$i] !="none" && $request->qty[$i] !="" && $request->unitprice[$i] !=""){
    //     //            $ist = new SysItemStock();
    //     //            $ist->doc_number = $ios->doc_number;
    //     //            $ist->doc_date = $ios->doc_date;
    //     //            $ist->os_id = $ios->id;
    //     //            $ist->partno = $request->part_number[$i];
    //     //            $ist->description = $request->description[$i];
    //     //            $ist->slno = "";
    //     //            $ist->qty_in = $request->qty[$i];
    //     //            $ist->price_in = $request->unitprice[$i];
    //     //            $ist->remarks = $request->remarks[$i];
    //     //            $ist->refno = $request->refno[$i];
    //     //            $ist->status = 1;
    //     //            $ist->created_by = Auth::user()->id;
    //     //            $ist->created_at = $trn_time;
    //     //            $ist->company_id = session('logged_session_data.company_id');
    //     //            $ist->save();
    //     //        }
    //     //    }


    //        $results=0;
    //        DB::commit();
    //         if ($results==0) {
    //             Toastr::success('Opening Stock has been added successfully', 'Success');
    //             return redirect()->back();
    //         } else {
    //             Toastr::error('Operation Failed', 'Failed');
    //             return redirect()->back();
    //         }

    //    } catch (\Exception $e) {
    //        return $e;
    //        DB::rollback();
    //        Toastr::error('Operation Failed', 'Failed');
    //        return redirect()->back();
    //    }

    // }

    public function store(Request $request)
    {
        


        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');


        $openingstock_account_id = SysHelper::get_opening_stock_account_id();
        if ($openingstock_account_id == "") {
            Toastr::error('Opening Stock Account Not Found. Please Create and Try Again', 'Failed');
            return redirect()->back();
        }
        $account_id = $openingstock_account_id;
        DB::beginTransaction();
        try {
            $ios = new SysItemOpeningStock();
            $ios->doc_number = SysHelper::get_new_code('sys_item_opening_stock', 'OP', 'doc_number');
            $ios->doc_date = Carbon::createFromFormat('d/m/Y', $request->doc_date)->format('Y-m-d');
            $ios->bill_date = Carbon::createFromFormat('d/m/Y', $request->bill_date)->format('Y-m-d');
            $ios->currency = $request->currency;
            $ios->narration = $request->remarks;
            $ios->status = 1;
            $ios->company_id = session('logged_session_data.company_id');
            $ios->created_by = Auth::user()->id;
            $ios->created_at = $trn_time;
            $ios->save();
            $ios->toArray();

            $draftOpsKeyScope = SysPurchaseGrnLicenseKey::where('type', 2)
                ->where('opening_stock_id', -1)
                ->where('cart_id', session('logged_session_data.cart_id'))
                ->where('company_id', session('logged_session_data.company_id'));

            if ((clone $draftOpsKeyScope)->count() > 0) {
                (clone $draftOpsKeyScope)->update([
                    'opening_stock_id' => $ios->id,
                    'cart_id' => '',
                    'updated_by' => Auth::user()->id,
                    'updated_at' => $trn_time,
                ]);
            }

            $opsLicenseRows = SysPurchaseGrnLicenseKey::where('type', 2)
                ->where('opening_stock_id', $ios->id)
                ->where('company_id', session('logged_session_data.company_id'))
                ->get();
            foreach ($opsLicenseRows as $licenseRow) {
                SysHelper::set_license_key_trn(
                    2,
                    $ios->id,
                    $ios->doc_date,
                    $ios->doc_number,
                    $licenseRow->id,
                    $licenseRow->item_id,
                    $licenseRow->license_key,
                    $licenseRow->exp_date
                );
            }
 
            //    if(count($cart)>0){
            //     foreach($cart as $itm){
            //         $ItemStockData[]=[
            //             'doc_number' => $ios->doc_number,
            //             'doc_date' => date('Y-m-d', strtotime($request->doc_date)),
            //             'ops_id' => $ios->id,
            //             'account_id' => $account_id,
            //             'partno' => $itm->part_number,
            //             'description' => $itm->description,
            //             'slno' => "",
            //             'qty_in' => $itm->qty,
            //             'price_in' => $itm->unitprice,
            //             'refno' => $itm->refno,
            //             'remarks' => "",
            //             'status' => 1,
            //             'created_by' => Auth::user()->id,
            //             'created_at' => $trn_time,
            //             'company_id' => session('logged_session_data.company_id'),
            //             'currency_id' => $request->currency,
            //         ];
            //     }
            //    }
            //    SysItemStock::insert($ItemStockData);

            $sum_value = 0;
            for ($i = 0; $i < count($request->part_number); $i++) {
           
                if ($request->part_number[$i] != "none" && $request->qty[$i] != "" && $request->unitprice[$i] != "") {
                    $ist = new SysItemStock();

                    $ist->doc_number = $ios->doc_number;
                    $ist->doc_date = $ios->doc_date;
                    $ist->ops_id = $ios->id;
                    $ist->account_id = $account_id;
                    $ist->partno = $request->part_number[$i];
                    $ist->description = $request->description[$i];
                    $ist->slno = $request->refno[$i];
                    $ist->qty_in = str_replace(',', '', $request->qty[$i]);
                    $ist->price_in = str_replace(',', '', $request->unitprice[$i]);
                    $ist->remarks = $request->narration[$i];
                    $ist->refno = $request->refno[$i];
                    $ist->status = 1;
                    $ist->created_by = Auth::user()->id;
                    $ist->created_at = $trn_time;
                    $ist->currency_id = $request->currency;
                    $ist->company_id = session('logged_session_data.company_id');
                    $ist->save();
                    $sum_value += str_replace(',', '', $request->value[$i]);


                }
            }

            $amount_dr = $sum_value;
            $amount_cr = '0.00';
            SysHelper::trn_chartof_accounts_transaction($account_id, $ios->id, $ios->doc_number, $ios->doc_date, 'openingstock', $amount_dr, $amount_cr, $request->narration, 1, 0, "", 0);

            DB::table('sys_stock_items_cart')->where('cart_id', session('logged_session_data.cart_id'))->delete();




            $results = 0;
            DB::commit();
            if ($results == 0) {
                Toastr::success('Opening Stock has been added successfully', 'Success');
                return redirect('item-store/show');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }

        } catch (\Exception $e) {
            return $e;
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }

    public function update(Request $request)
    {
        DB::beginTransaction(); // Start transaction
        try {
            $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $ios = SysItemOpeningStock::find($request->id);
            $ios->doc_date = Carbon::createFromFormat('d/m/Y', $request->doc_date)->format('Y-m-d');
            $ios->bill_date = Carbon::createFromFormat('d/m/Y', $request->bill_date)->format('Y-m-d');
            $ios->currency = $request->currency;
            $ios->narration = $request->remarks;
            $ios->status = 1;
            $ios->updated_by = Auth::user()->id;
            $ios->updated_at = $trn_time;
            $ios->save();



            // For add item
            $ios->items()->delete();

            $ios->toArray();


            $sum_value = 0;
            for ($i = 0; $i < count($request->part_number); $i++) {
                if ($request->part_number[$i] != "none" && $request->qty[$i] != "" && $request->unitprice[$i] != "") {
                    $ist = new SysItemStock();
                    $ist->doc_number = $ios->doc_number;
                    $ist->doc_date = $ios->doc_date;

                    $ist->ops_id = $ios->id;
                    $ist->partno = $request->part_number[$i];
                    $ist->description = $request->description[$i];
                    $ist->slno = $request->refno[$i];
                    $ist->qty_in = str_replace(',', '', $request->qty[$i]);
                    $ist->price_in = str_replace(',', '', $request->unitprice[$i]);
                    $ist->remarks = $request->narration[$i];
                    $ist->refno = $request->refno[$i];
                    $ist->status = 1;
                    $ist->created_by = Auth::user()->id;
                    $ist->created_at = $trn_time;
                    $ist->currency_id = $request->currency;
                    $ist->company_id = session('logged_session_data.company_id');
                    $ist->save();
                    $sum_value += str_replace(',', '', $request->value[$i]);
                }
            }



            $dr_amount = SysItemStock::where('doc_number', $ios->doc_number)->sum(DB::raw('price_in * qty_in'));


            SysChartofAccountsTransaction::where('transaction_no', $ios->doc_number)->update([
                'debit_amount' => $dr_amount,
            ]);

            DB::commit(); // Commit all queries

            Toastr::success('Opening Stock has been updated successfully', 'Success');
            return redirect('item-store/show');

        } catch (\Throwable $th) {
            DB::rollBack(); // Rollback all queries
            return $th;
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
    public function show()
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $ios = SysItemOpeningStock::wherein('company_id', $company_id)->get();

            return view('backEnd.inventory.itemStoreList', compact('ios'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            //return redirect()->back();
            return $e;
        }
    }

    public function stockregister_exe(Request $request)
    {
        try{
            $brand = SysBrand::select('id','title')->orderby('title','asc')->get();
            $category = DB::table('sm_item_categories')->select('id','category_name')->orderby('category_name','asc')->get();
            $sub_category = DB::table('sm_item_subcategories')->select('id','sub_category_name')->orderby('sub_category_name','asc')->get();

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $to_date = date('Y-m-d');
            $stocklist = [];
            $stocklist_return = [];
            $r_part_number=""; $r_brand=""; $r_category=""; $r_sub_category=""; $r_qty="";
            if($_POST){
                $to_date = $request->to_date;
                if($to_date==""){ $to_date = date('Y-m-d'); }
                
                $stocklist_query = DB::table('sys_item_stock as stock')
                ->select(DB::raw('max(item.part_number) as part_number'),DB::raw('max(stock.partno) as partno'),DB::raw('max(item.description) as description')
                ,DB::raw('max(brand.title) as brand'),DB::raw('max(brand.id) as brandid'),DB::raw('SUM(stock.qty_in) - SUM(stock.qty_out) as balance_qty')
                
                ,DB::raw('(SUM(stock.qty_in) * sum(stock.price_in)) / SUM(stock.qty_in) as avg_price')

                ,DB::raw('max(cat.category_name) as categoryname'),DB::raw('max(subcat.sub_category_name) as subcategoryname'))
                ->selectRaw('2 as type')
                ->join('sm_items as item', 'item.id','stock.partno')
                ->join('sys_brand as brand','brand.id','item.brand')
                ->leftjoin('sm_item_categories as cat','cat.id','item.category_name')
                ->leftjoin('sm_item_subcategories as subcat','subcat.id','item.subcategory_name')
                ->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') <= '" . $to_date . "'")
                ->wherein('stock.company_id',$company_id)->where('stock.status',1)->where('item.status',1)
                //->where('stock.doc_number', 'not like', 'SR%')
                ->wherein('item.product_type',[1,2]);
                
                if($request->part_number != ""){
                    $stocklist_query->where('item.part_number','like','%'.$request->part_number.'%');
                    
                    $r_part_number = $request->part_number;
                }
                if($request->brand != ""){
                    $stocklist_query->where('item.brand',$request->brand);
                    $r_brand = $request->brand;
                }
                if($request->category != ""){
                    $stocklist_query->where('item.category_name',$request->category);
                    $r_category = $request->category;
                }
                if($request->sub_category != ""){
                    $stocklist_query->where('item.subcategory_name',$request->sub_category);
                    $r_sub_category = $request->sub_category;
                }
                if($request->qty != ""){
                    $r_qty = $request->qty;
                }

                $stocklist = $stocklist_query->groupby('stock.partno')
                ->orderBy('part_number', 'asc')
                ->get(); //->paginate(100);//


                $stocklist_return = DB::table('sys_item_stock')->select(DB::raw('max(partno) as partno'),DB::raw('SUM(qty_in) as qty'))
                ->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') <= '" . $to_date . "'")->wherein('company_id',$company_id)->where('doc_number', 'like', 'SR%')->where('status',1)
                ->groupby('partno')->get();


                
                /*$result = Customer::select([
                    'customers.id',
                    'customers.last_name'
                ])->withCount([
                    'customerInvoices as invoice_sum' => function($query) {
                        $query->select(DB::raw('SUM(total_price)'));
                    }
                ])->whereHas('customerInvoices', function(Builder $q) {
                    $q->where('customer_invoices.status', 1);
                })->get();*/

                //DB::raw("IF(book.visibility_school = 1, IF(schools.id = ?, 1, 0), 1) = 1"),$currentUserSchoolId)

                //return $stocklist;

                /*$sql = "SELECT MAX(item.part_number) part_number, MAX(item.description) description, MAX(br.title) brand,
                SUM(stock.qty_in)qty_in, SUM(stock.qty_out)qty_out, SUM(stock.qty_in-stock.qty_out)qty_bal,
                SUM(stock.qty_in*stock.price_in)qty_price, SUM(stock.qty_in*stock.price_in/stock.qty_in)avg_price
                FROM sys_item_stock stock
                JOIN sm_items item ON item.id = stock.partno
                JOIN sys_brand br ON br.id = item.brand
                WHERE DATE_FORMAT(stock.doc_date, '%Y-%m-%d') <= '". $to_date ."' GROUP BY stock.partno";
                $stocklist = DB::select($sql);*/
            }
            else{
                
                /*$stocklist = DB::table('sys_item_stock as stock')
                ->select(DB::raw('max(item.part_number) as part_number'),DB::raw('max(stock.partno) as partno'),DB::raw('max(item.description) as description')
                ,DB::raw('max(brand.title) as brand'),DB::raw('max(brand.id) as brandid'),DB::raw('SUM(stock.qty_in) - SUM(stock.qty_out) as balance_qty')
                ,DB::raw('SUM(stock.qty_in * stock.price_in) / SUM(stock.qty_in) as avg_price')
                ,DB::raw('max(cat.category_name) as categoryname'),DB::raw('max(subcat.sub_category_name) as subcategoryname'))
                ->selectRaw('2 as type')
                ->join('sm_items as item', 'item.id','stock.partno')
                ->join('sys_brand as brand','brand.id','item.brand')
                ->leftjoin('sm_item_categories as cat','cat.id','item.category_name')
                ->leftjoin('sm_item_subcategories as subcat','subcat.id','item.subcategory_name')
                ->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') <= '" . $to_date . "'")
                ->wherein('stock.company_id',$company_id)->where('stock.status',1)->where('item.status',1)
                ->where('stock.doc_number', 'not like', 'SR%')
                ->wherein('item.product_type',[1,2])
                ->groupby('item.part_number','item.description','brand.title')
                ->get();            

                $stocklist_return = DB::table('sys_item_stock')->select(DB::raw('max(partno) as partno'),DB::raw('SUM(qty_in) as qty'))
                ->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') <= '" . $to_date . "'")->wherein('company_id',$company_id)->where('doc_number', 'like', 'SR%')->where('status',1)
                ->groupby('partno')->get();*/
                $stocklist = DB::table('sys_item_stock as stock')
                    ->select([
                        DB::raw('MAX(item.part_number) as part_number'),
                        DB::raw('MAX(stock.partno) as partno'),
                        DB::raw('MAX(item.description) as description'),
                        DB::raw('MAX(brand.title) as brand'),
                        DB::raw('MAX(brand.id) as brandid'),
                        DB::raw('(SUM(stock.qty_in) - SUM(stock.qty_out)) as balance_qty'),
                        DB::raw('IFNULL(SUM(stock.qty_in * stock.price_in) / NULLIF(SUM(stock.qty_in), 0), 0) as avg_price'),
                        DB::raw('MAX(cat.category_name) as categoryname'),
                        DB::raw('MAX(subcat.sub_category_name) as subcategoryname'),
                        DB::raw('2 as type'),
                    ])
                    ->join('sm_items as item', 'item.id', '=', 'stock.partno')
                    ->join('sys_brand as brand', 'brand.id', '=', 'item.brand')
                    ->leftJoin('sm_item_categories as cat', 'cat.id', '=', 'item.category_name')
                    ->leftJoin('sm_item_subcategories as subcat', 'subcat.id', '=', 'item.subcategory_name')
                    ->whereDate('stock.doc_date', '<=', $to_date)
                    ->whereIn('stock.company_id', $company_id)
                    ->where('stock.status', 1)
                    ->where('item.status', 1)
                    //->where('stock.doc_number', 'not like', 'SR%')
                    ->whereIn('item.product_type', [1, 2])
                    ->groupBy('stock.partno') // use the unique stock ID for grouping
                    // ->paginate(100);
                    ->orderBy('part_number', 'asc')
                    ->get();

                    $stocklist_return = DB::table('sys_item_stock')
                        ->select([
                            DB::raw('partno'),
                            DB::raw('SUM(qty_in) as qty')
                        ])
                        ->whereDate('doc_date', '<=', $to_date)
                        ->whereIn('company_id', $company_id)
                        ->where('doc_number', 'like', 'SR%')
                        ->where('status', 1)
                        ->groupBy('partno')
                        ->get()
                        ->keyBy('partno');
            }
            
            if(Auth::user()->role_id==1 || Auth::user()->role_id==28 || Auth::user()->role_id==27){
                $show_all=1;
            }
            else{
                $show_all=0;
            }
            $user = SmStaff::select('brands')->where('user_id',Auth::user()->id)->first();
            if($user->brands==""){
                $show_brand = [];
            } else {
                $show_brand = explode(',', $user->brands);
            }
            $company_list = DB::table('sys_company')->select('id','company_name')->orderby('sort_id','asc')->get();
            $stockledgerBalances = collect([]);
            return view('backEnd.inventory.StockRegister', compact('stocklist','to_date','stocklist_return','stockledgerBalances','brand','category','sub_category','r_part_number','r_brand','r_category','r_sub_category','r_qty','company_list','show_all','show_brand'));
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function stockregister(Request $request)
    {

        try {
            $brand = SysBrand::select('id','title')->orderby('title','asc')->get();
            $category = DB::table('sm_item_categories')->select('id','category_name')->orderby('category_name','asc')->get();
            $sub_category = DB::table('sm_item_subcategories')->select('id','sub_category_name')->orderby('sub_category_name','asc')->get();

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $to_date = date('Y-m-d');
            $stocklist = [];
            $stocklist_return = [];
            $ctrl_product_type = "";

            $producttype = SysProductType::get();


            $r_part_number = "";
            $r_brand = "";
            $r_category = "";
            $r_sub_category = "";
            $r_qty = "";
            if ($_POST) {
                $to_date = $request->to_date;

                if (empty($to_date)) {
                    $to_date = Carbon::now()->format('Y-m-d'); // for internal use (e.g., query)
                } else {
                    $to_date = Carbon::createFromFormat('d/m/Y', $to_date)->format('Y-m-d');
                }
                // if($to_date==""){ $to_date = date('Y-m-d'); }

                $stocklist_query = DB::table('sys_item_stock as stock')
                ->select(DB::raw('max(item.part_number) as part_number'),DB::raw('max(stock.partno) as partno'),DB::raw('max(item.description) as description')
                ,DB::raw('max(brand.title) as brand'),DB::raw('max(brand.id) as brandid'),DB::raw('SUM(stock.qty_in) - SUM(stock.qty_out) as balance_qty')
                
                ,DB::raw('(SUM(stock.qty_in) * sum(stock.price_in)) / SUM(stock.qty_in) as avg_price')

                ,DB::raw('max(cat.category_name) as categoryname'),DB::raw('max(subcat.sub_category_name) as subcategoryname'))
                ->selectRaw('2 as type')
                ->join('sm_items as item', 'item.id','stock.partno')
                ->join('sys_brand as brand','brand.id','item.brand')
                ->leftjoin('sm_item_categories as cat','cat.id','item.category_name')
                ->leftjoin('sm_item_subcategories as subcat','subcat.id','item.subcategory_name')
                ->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') <= '" . $to_date . "'")
                ->wherein('stock.company_id',$company_id)->where('stock.status',1)->where('item.status',1)
                //->where('stock.doc_number', 'not like', 'SR%')
                ->wherein('item.product_type',[1,2]);

                if ($request->part_number != "") {

                    $part_numbers = array_map('trim', explode(',', $request->part_number));

                    $stocklist_query->whereIn('item.part_number', $part_numbers);

                    $r_part_number = $request->part_number;
                    // $stocklist_query->where('item.part_number','like','%'.$request->part_number.'%');

                    // $r_part_number = $request->part_number;
                }
                if ($request->brand != "") {
                    $stocklist_query->where('item.brand', $request->brand);
                    $r_brand = $request->brand;
                }
                if ($request->category != "") {
                    $stocklist_query->where('item.category_name', $request->category);
                    $r_category = $request->category;
                }
                if ($request->sub_category != "") {
                    $stocklist_query->where('item.subcategory_name', $request->sub_category);
                    $r_sub_category = $request->sub_category;
                }
                if ($request->qty != "") {
                    $r_qty = $request->qty;
                }

                if ($request->filter_product_type != "") {
                    $ctrl_product_type = $request->filter_product_type;
                    $stocklist_query->where('item.product_type', $request->filter_product_type);
                }

                $stocklist = $stocklist_query->groupby('stock.partno')
                ->orderBy('part_number', 'asc')
                ->get(); //->paginate(100);//


                $stocklist_return = DB::table('sys_item_stock')->select(DB::raw('max(partno) as partno'),DB::raw('SUM(qty_in) as qty'))
                ->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') <= '" . $to_date . "'")->wherein('company_id',$company_id)->where('doc_number', 'like', 'SR%')->where('status',1)
                ->groupby('partno')->get();

                $stockledgerBalancesQuery = DB::table('sys_item_stock as stock')
                    ->select('stock.partno', DB::raw('SUM(stock.qty_in) - SUM(stock.qty_out) as ledger_balance'))
                    ->join('sm_items as item', 'item.id', 'stock.partno')
                    ->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') <= '" . $to_date . "'")
                    ->wherein('stock.company_id', $company_id)
                    ->where('stock.status', 1)
                    ->where('item.status', 1);

                if ($request->part_number != "") {
                    $part_numbers = array_map('trim', explode(',', $request->part_number));
                    $stockledgerBalancesQuery->whereIn('item.part_number', $part_numbers);
                }
                if ($request->brand != "") {
                    $stockledgerBalancesQuery->where('item.brand', $request->brand);
                }
                if ($request->category != "") {
                    $stockledgerBalancesQuery->where('item.category_name', $request->category);
                }
                if ($request->sub_category != "") {
                    $stockledgerBalancesQuery->where('item.subcategory_name', $request->sub_category);
                }
                if ($request->filter_product_type != "") {
                    $stockledgerBalancesQuery->where('item.product_type', $request->filter_product_type);
                }

                $stockledgerBalances = $stockledgerBalancesQuery->groupBy('stock.partno')->pluck('ledger_balance', 'partno');

                
                /*$result = Customer::select([
                    'customers.id',
                    'customers.last_name'
                ])->withCount([
                    'customerInvoices as invoice_sum' => function($query) {
                        $query->select(DB::raw('SUM(total_price)'));
                    }
                ])->whereHas('customerInvoices', function(Builder $q) {
                    $q->where('customer_invoices.status', 1);
                })->get();*/

                //DB::raw("IF(book.visibility_school = 1, IF(schools.id = ?, 1, 0), 1) = 1"),$currentUserSchoolId)

                //return $stocklist;

                /*$sql = "SELECT MAX(item.part_number) part_number, MAX(item.description) description, MAX(br.title) brand,
                SUM(stock.qty_in)qty_in, SUM(stock.qty_out)qty_out, SUM(stock.qty_in-stock.qty_out)qty_bal,
                SUM(stock.qty_in*stock.price_in)qty_price, SUM(stock.qty_in*stock.price_in/stock.qty_in)avg_price
                FROM sys_item_stock stock
                JOIN sm_items item ON item.id = stock.partno
                JOIN sys_brand br ON br.id = item.brand
                WHERE DATE_FORMAT(stock.doc_date, '%Y-%m-%d') <= '". $to_date ."' GROUP BY stock.partno";
                $stocklist = DB::select($sql);*/
            } else {

                
                // $stocklist = DB::table('sys_item_stock as stock')
                //     ->select([
                //         DB::raw('MAX(stock.partno) as partno'),
                //         DB::raw('MAX(item.part_number) as part_number'),
                //         DB::raw('MAX(item.id) as stockid'),
                //         DB::raw('MAX(item.description) as description'),
                //         DB::raw('MAX(brand.title) as brand'),
                //         DB::raw('MAX(brand.id) as brandid'),
                //         DB::raw('(SUM(stock.qty_in) - SUM(stock.qty_out)) as balance_qty'),
                //         DB::raw('IFNULL(SUM(stock.qty_in * stock.price_in) / NULLIF(SUM(stock.qty_in), 0), 0) as avg_price'),
                //         DB::raw('MAX(cat.category_name) as categoryname'),
                //         DB::raw('MAX(subcat.sub_category_name) as subcategoryname'),
                //         DB::raw('2 as type'),
                //     ])
                //     ->join('sm_items as item', 'item.id', '=', 'stock.partno')
                //     ->join('sys_brand as brand', 'brand.id', '=', 'item.brand')
                //     ->leftJoin('sm_item_categories as cat', 'cat.id', '=', 'item.category_name')
                //     ->leftJoin('sm_item_subcategories as subcat', 'subcat.id', '=', 'item.subcategory_name')
                //     ->whereDate('stock.doc_date', '<=', $to_date)
                //     ->whereIn('stock.company_id', $company_id)
                //     ->where('stock.status', 1)
                //     ->where('item.status', 1)
                //     ->where('stock.doc_number', 'not like', 'SRN%')
                //     ->whereIn('item.product_type', [1, 2])
                //     ->groupBy('stock.partno')
                //     ->orderByRaw('MAX(item.part_number) ASC') // use the unique stock ID for grouping
                //     // ->paginate(100);
                // ->get();

                 $stocklist = [];

                $stocklist_return = DB::table('sys_item_stock')
                    ->select([
                        DB::raw('partno'),
                        DB::raw('SUM(qty_in) as qty')
                    ])
                    ->whereDate('doc_date', '<=', $to_date)
                    ->whereIn('company_id', $company_id)
                    ->where('doc_number', 'like', 'SRN%')
                    ->where('status', 1)
                    ->groupBy('partno')
                    ->get()
                    ->keyBy('partno');

                $stockledgerBalances = collect([]);
            }

            if (Auth::user()->role_id == 1 || Auth::user()->role_id == 28 || Auth::user()->role_id == 27) {
                $show_all = 1;
            } else {
                $show_all = 0;
            }
            $user = SmStaff::select('brands')->where('user_id', Auth::user()->id)->first();
            if ($user->brands == "") {
                $show_brand = [];
            } else {
                $show_brand = explode(',', $user->brands);
            }

            $company_list = DB::table('sys_company')->select('id', 'company_name')->orderby('sort_id', 'asc')->get();

            $this->cleanupExpiredReserveStock();

           

            // dd($stocklist);

            return view('backEnd.inventory.StockRegister', compact('stocklist', 'to_date', 'stocklist_return', 'stockledgerBalances', 'brand', 'category', 'sub_category', 'r_part_number', 'r_brand', 'r_category', 'r_sub_category', 'r_qty', 'company_list', 'show_all', 'show_brand', 'ctrl_product_type', 'producttype'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function stockregister_test(Request $request)
    {
        try {

            $r_part_number = "";
            $r_brand = "";
            $r_category = "";
            $r_sub_category = "";
            $r_qty = "";
            $to_date = date('Y-m-d');
            $stocklist = [];
            $stocklist_return = [];

            $brand = SysBrand::select('id', 'title')->orderby('title', 'asc')->get();
            $category = DB::table('sm_item_categories')->select('id', 'category_name')->orderby('category_name', 'asc')->get();
            $sub_category = DB::table('sm_item_subcategories')->select('id', 'sub_category_name')->orderby('sub_category_name', 'asc')->get();

            $user = Auth::user();
            $user_id = $user->id;
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $to_date = $request->filled('to_date') ? $request->input('to_date') : date('Y-m-d');
            $page = $request->get('page', 1);
            $perPage = 50;

            $stocklist_query = DB::table('sys_item_stock as stock')
                ->select(
                    DB::raw('max(item.part_number) as part_number'),
                    DB::raw('max(stock.partno) as partno'),
                    DB::raw('max(item.description) as description'),
                    DB::raw('max(brand.title) as brand'),
                    DB::raw('max(brand.id) as brandid'),
                    DB::raw('SUM(stock.qty_in) - SUM(stock.qty_out) as balance_qty'),
                    DB::raw('SUM(stock.qty_in * stock.price_in) / NULLIF(SUM(stock.qty_in), 0) as avg_price'),
                    DB::raw('max(cat.category_name) as categoryname'),
                    DB::raw('max(subcat.sub_category_name) as subcategoryname')
                )
                ->selectRaw('2 as type')
                ->join('sm_items as item', 'item.id', 'stock.partno')
                ->join('sys_brand as brand', 'brand.id', 'item.brand')
                ->leftJoin('sm_item_categories as cat', 'cat.id', 'item.category_name')
                ->leftJoin('sm_item_subcategories as subcat', 'subcat.id', 'item.subcategory_name')
                ->whereDate('stock.doc_date', '<=', $to_date)
                ->whereIn('stock.company_id', $company_id)
                ->where('stock.status', 1)
                ->where('item.status', 1)
                ->where('stock.doc_number', 'not like', 'SRN%')
                ->whereIn('item.product_type', [1, 2])
                ->groupBy('item.part_number', 'item.description', 'brand.title');

            $stocklist = $stocklist_query->paginate($perPage);


            $stocklist_return = DB::table('sys_item_stock')
                ->select([
                    DB::raw('partno'),
                    DB::raw('SUM(qty_in) as qty')
                ])
                ->whereDate('doc_date', '<=', $to_date)
                ->whereIn('company_id', $company_id)
                ->where('doc_number', 'like', 'SRN%')
                ->where('status', 1)
                ->groupBy('partno')
                ->get()
                ->keyBy('partno');

            $company_list = DB::table('sys_company')->select('id', 'company_name')->orderby('sort_id', 'asc')->get();

            if (Auth::user()->role_id == 1 || Auth::user()->role_id == 28 || Auth::user()->role_id == 27) {
                $show_all = 1;
            } else {
                $show_all = 0;
            }
            $user = SmStaff::select('brands')->where('user_id', Auth::user()->id)->first();
            if ($user->brands == "") {
                $show_brand = [];
            } else {
                $show_brand = explode(',', $user->brands);
            }

            if ($request->ajax()) {
                return response()->json([
                    'data' => view('backEnd.inventory.stock_rows', compact('stocklist', 'to_date', 'stocklist_return', 'brand', 'category', 'sub_category', 'r_part_number', 'r_brand', 'r_category', 'r_sub_category', 'r_qty', 'company_list', 'show_all', 'show_brand'))->render()
                ]);
            }

            return view('backEnd.inventory.StockRegister2', compact('stocklist', 'to_date', 'stocklist_return', 'brand', 'category', 'sub_category', 'r_part_number', 'r_brand', 'r_category', 'r_sub_category', 'r_qty', 'company_list', 'show_all', 'show_brand'));

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function get_stock_register_group_qty(Request $request)
    {
        try {
    
            $to_date = date('Y-m-d');
    
            $data = DB::table('sys_item_stock as stock')
                ->select(
                    DB::raw('max(stock.partno) as partno'),
                    DB::raw('SUM(stock.qty_in) - SUM(stock.qty_out) as balance_qty'),
                    'stock.company_id'
                )
                ->where('stock.status', 1)
                ->where('stock.doc_number', 'not like', 'SRN%')
                ->where('stock.partno', $request->partno)
                ->groupBy('stock.company_id')
                ->get();
    
            foreach ($data as $row) {
    
                $avgRate =
                    SysHelper::get_stock_register_ledger_avg_rate(
                        $request->partno,
                        $to_date,
                        $row->company_id
                    );
    
                $row->avg_price = $avgRate;
                $row->value =
                    (float)$row->balance_qty * (float)$avgRate;
            }
    
            return response()->json([
                'data' => $data
            ]);
    
        } catch (\Exception $e) {
    
            return response()->json([
                'data' => []
            ]);
        }
    }

    public function stock_search(Request $request)
    {
        try {

            $part_number = "";
            $data_list = [];
            $show_all = 0;
            if (request()->ajax()) {
                $part_number = $request->part_number;
            }
            if ($_POST) {
                $part_number = $request->part_number;
            }

            // Get stock return data (SRN documents)
            $stocklist_return_query = DB::table('sys_item_stock as stock')
                ->select(
                    DB::raw('stock.partno as partno'),
                    DB::raw('stock.company_id as company_id'),
                    DB::raw('SUM(stock.qty_in) as return_qty')
                )
                ->join('sm_items as i', 'i.id', 'stock.partno')
                ->where('stock.status', 1)
                ->where('stock.doc_number', 'like', 'SRN%');

            if ($part_number != "") {
                $stocklist_return_query->where(function ($query) use ($part_number) {
                    $query->where('i.part_number', 'like', '%' . $part_number . '%')
                        ->orWhere('i.description', 'like', '%' . $part_number . '%');
                });
            }

            $stocklist_return = $stocklist_return_query
                ->groupby('stock.partno', 'stock.company_id')
                ->get()
                ->keyBy(function ($item) {
                    return $item->partno . '_' . $item->company_id;
                });

            // Main stock query (excluding SRN)
            $data_query = DB::table('sys_item_stock as stock')
                ->select(
                    DB::raw('max(stock.id) as stock_id'),
                    DB::raw('max(stock.partno) as partno'),
                    DB::raw('max(i.part_number) as part_number'),
                    DB::raw('max(i.description) as description'),
                    DB::raw('max(c.company_name) as company_name'),
                    DB::raw('SUM(stock.qty_in) - SUM(stock.qty_out) as balance_qty'),
                    DB::raw('IFNULL(SUM(stock.qty_in * stock.price_in) / NULLIF(SUM(stock.qty_in), 0), 0) as avg_price'),
                    'stock.company_id'
                )
                ->join('sm_items as i', 'i.id', 'stock.partno')
                ->join('sys_company as c', 'c.id', 'stock.company_id')
                //->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') <= '" . $to_date . "'")
                ->where('stock.status', 1)
                ->where('stock.doc_number', 'not like', 'SRN%');

            if ($part_number != "") {
                $data_query->where(function ($query) use ($part_number) {
                    $query->where('i.part_number', 'like', '%' . $part_number . '%')
                        ->orWhere('i.description', 'like', '%' . $part_number . '%');
                });
            } else {
                $data_query->where('i.part_number', '00000000');
            }

            $data_query->groupby('stock.company_id')
                ->orderby('part_number', 'asc');

            if (Auth::user()->role_id == 1 || Auth::user()->role_id == 28 || Auth::user()->role_id == 27) {
                $show_all = 1;
            } else {
                $show_all = 0;
            }
            $data_list = $data_query->paginate(100);

            // Add return quantities to balance_qty (same as stockregister)
            foreach ($data_list as $item) {
                $key = $item->partno . '_' . $item->company_id;
                if (isset($stocklist_return[$key])) {
                    $item->balance_qty += $stocklist_return[$key]->return_qty;
                }
            }

            return View('backEnd.inventory.StockSearch', compact('part_number', 'data_list', 'show_all', 'stocklist_return'))->render();

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function listprice(Request $request)
    {
        try {
            $brand = SysBrand::select('id', 'title')->orderby('title', 'asc')->get();
            $category = DB::table('sm_item_categories')->select('id', 'category_name')->orderby('category_name', 'asc')->get();
            $sub_category = DB::table('sm_item_subcategories')->select('id', 'sub_category_name')->orderby('sub_category_name', 'asc')->get();

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $to_date = date('Y-m-d');
            $stocklist = [];
            $stocklist_return = [];
            $r_part_number = "";
            $r_brand = "";
            $r_category = "";
            $r_sub_category = "";
            $r_qty = "";
            if ($_POST) {
                $to_date = $request->to_date;
                if ($to_date == "") {
                    $to_date = date('Y-m-d');
                }

                if (empty($to_date)) {
                    $to_date = Carbon::now()->format('Y-m-d'); // for internal use (e.g., query)
                } else {
                    $to_date = Carbon::createFromFormat('d/m/Y', $to_date)->format('Y-m-d');
                }

                $stocklist_query = DB::table('sys_item_stock as stock')
                    ->select(
                        DB::raw('max(item.part_number) as part_number'),
                        DB::raw('max(stock.partno) as partno'),
                        DB::raw('max(item.description) as description')
                        ,
                        DB::raw('max(brand.title) as brand'),
                        DB::raw('SUM(stock.qty_in) - SUM(stock.qty_out) as balance_qty')
                        ,
                        DB::raw('SUM(stock.qty_in * stock.price_in) / SUM(stock.qty_in) as avg_price')
                        ,
                        DB::raw('max(cat.category_name) as categoryname'),
                        DB::raw('max(subcat.sub_category_name) as subcategoryname')
                    )

                    ->addSelect(DB::raw('(SELECT unitprice FROM sys_purchase_grn_items as grnit join sys_purchase_grn as grn on grnit.grn_id= grn.id where part_no = item.id and grn.company_id=' . $company_id[0] . '  ORDER BY grn_id DESC LIMIT 1) as lp_price'))

                    ->join('sm_items as item', 'item.id', 'stock.partno')
                    ->join('sys_brand as brand', 'brand.id', 'item.brand')
                    ->leftjoin('sm_item_categories as cat', 'cat.id', 'item.category_name')
                    ->leftjoin('sm_item_subcategories as subcat', 'subcat.id', 'item.subcategory_name')
                    ->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') <= '" . $to_date . "'")
                    ->wherein('stock.company_id', $company_id)->where('stock.status', 1)
                    ->where('stock.doc_number', 'not like', 'SRN%');

                if ($request->part_number != "") {
                    $stocklist_query->where('item.part_number', 'like', '%' . $request->part_number . '%');
                    $r_part_number = $request->part_number;
                }
                if ($request->brand != "") {
                    $stocklist_query->where('item.brand', $request->brand);
                    $r_brand = $request->brand;
                }
                if ($request->category != "") {
                    $stocklist_query->where('item.category_name', $request->category);
                    $r_category = $request->category;
                }
                if ($request->sub_category != "") {
                    $stocklist_query->where('item.subcategory_name', $request->sub_category);
                    $r_sub_category = $request->sub_category;
                }
                if ($request->qty != "") {
                    $r_qty = $request->qty;
                }

                $stocklist = $stocklist_query->groupby('item.part_number', 'item.description', 'brand.title', 'item.id')->get();

                $stocklist_return = DB::table('sys_item_stock')->select(DB::raw('max(partno) as partno'), DB::raw('SUM(qty_in) as qty'))
                    ->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') <= '" . $to_date . "'")->wherein('company_id', $company_id)->where('doc_number', 'like', 'SRN%')->where('status', 1)
                    ->groupby('partno')->get();

            } else {

                $stocklist = DB::table('sys_item_stock as stock')
                    ->select(
                        DB::raw('max(item.part_number) as part_number'),
                        DB::raw('max(stock.partno) as partno'),
                        DB::raw('max(item.description) as description')
                        ,
                        DB::raw('max(brand.title) as brand'),
                        DB::raw('SUM(stock.qty_in) - SUM(stock.qty_out) as balance_qty')
                        ,
                        DB::raw('SUM(stock.qty_in * stock.price_in) / SUM(stock.qty_in) as avg_price')
                        ,
                        DB::raw('max(cat.category_name) as categoryname'),
                        DB::raw('max(subcat.sub_category_name) as subcategoryname')
                    )

                    ->addSelect(DB::raw('(SELECT unitprice FROM sys_purchase_grn_items as grnit join sys_purchase_grn as grn on grnit.grn_id= grn.id where part_no = item.id and grn.company_id=' . $company_id[0] . '  ORDER BY grn_id DESC LIMIT 1) as lp_price'))

                    ->join('sm_items as item', 'item.id', 'stock.partno')
                    ->join('sys_brand as brand', 'brand.id', 'item.brand')
                    ->leftjoin('sm_item_categories as cat', 'cat.id', 'item.category_name')
                    ->leftjoin('sm_item_subcategories as subcat', 'subcat.id', 'item.subcategory_name')
                    ->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') <= '" . $to_date . "'")
                    ->wherein('stock.company_id', $company_id)->where('stock.status', 1)
                    ->where('stock.doc_number', 'not like', 'SRN%')
                    ->groupby('item.part_number', 'item.description', 'brand.title', 'item.id')
                    ->get();



                $stocklist_return = DB::table('sys_item_stock')->select(DB::raw('max(partno) as partno'), DB::raw('SUM(qty_in) as qty'))
                    ->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') <= '" . $to_date . "'")->wherein('company_id', $company_id)->where('doc_number', 'like', 'SRN%')->where('status', 1)
                    ->groupby('partno')->get();

            }
            return view('backEnd.inventory.ListPrice', compact('stocklist', 'to_date', 'stocklist_return', 'brand', 'category', 'sub_category', 'r_part_number', 'r_brand', 'r_category', 'r_sub_category', 'r_qty'));
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
    public function edit(Request $request, $id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));

            $items = SysHelper::get_product_list($company_id);

            $openingstock = SysItemOpeningStock::where('id', $id)->first();
            $stocklist = SysItemStock::where('ops_id', $id)->orderby('id', 'asc')->get();

            return view('backEnd.inventory.itemStoreFormEdit', compact('currency', 'items', 'company', 'openingstock', 'stocklist'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }


        $editData = SmItemStore::find($id);
        $itemstores = SmItemStore::all();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['editData'] = $editData->toArray();
            $data['itemstores'] = $itemstores->toArray();
            return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.inventory.itemStoreList', compact('editData', 'itemstores'));
    }
    public function additem(Request $request)
    {
        try {
            $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $ist = new SysItemStock();
            $ist->doc_number = $request->doc_number;
            $ist->doc_date = $request->doc_date;
            $ist->ops_id = $request->os_id;
            $ist->partno = $request->part_number;
            $ist->description = $request->description;
            $ist->slno = "";
            $ist->qty_in = $request->qty;
            $ist->price_in = $request->unitprice;
            $ist->remarks = $request->remarks;
            $ist->refno = $request->refno;
            $ist->status = 1;
            $ist->created_by = Auth::user()->id;
            $ist->created_at = $trn_time;
            $ist->company_id = session('logged_session_data.company_id');
            $ist->currency_id = $request->currency_ids;
            $ist->save();

            $dr_amount = SysItemStock::where('doc_number', $request->doc_number)->sum(DB::raw('price_in * qty_in'));

            SysChartofAccountsTransaction::where('transaction_no', $request->doc_number)->update([
                'debit_amount' => $dr_amount,
            ]);

            Toastr::success('Added Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            //return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function updateitem(Request $request)
    {
        try {
            DB::table('sys_item_stock')->where('id', $request->id)
                ->update([
                    'description' => $request->description,
                    'qty_in' => $request->qty,
                    'price_in' => $request->price,
                    'remarks' => $request->remarks,
                    'refno' => $request->refno,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $dr_amount = SysItemStock::where('doc_number', $request->doc_number)->sum(DB::raw('price_in * qty_in'));
            SysChartofAccountsTransaction::where('transaction_no', $request->doc_number)->update([
                'debit_amount' => $dr_amount,
            ]);

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

    public function deleteitem(Request $request)
    {
        $input = $request->all();

        try {
            DB::table('sys_item_stock')->where('id', $request->id)->delete();

            $dr_amount = SysItemStock::where('doc_number', $request->doc_number)->sum(DB::raw('price_in * qty_in'));
            SysChartofAccountsTransaction::where('transaction_no', $request->doc_number)->update([
                'debit_amount' => $dr_amount,
            ]);

            $bug = 0;
        } catch (\Exception $e) {
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


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        try {
            SysItemOpeningStock::where('doc_number', $id)->delete();
            SysItemStock::where('doc_number', $id)->delete();
            SysChartofAccountsTransaction::where('transaction_no', $id)->delete();

            Toastr::success('Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            //return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
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



    // import

    public function item_store_import(Request $request)
    {
        try {
            $data = DB::table('sys_item_stock_import as i')->select('i.*')->where('i.status', 1)->where('i.company_id', session('logged_session_data.company_id'))->get();
            $partnumber = DB::table('sm_items')->select('part_number')->where('status', 1)->get();

            return view('backEnd.inventory.importopeningstock', compact('data', 'partnumber'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function item_store_import_list(Request $request)
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
                    $dataArray[0][0] => trim($dataArray[$i][0]),
                    $dataArray[0][1] => $dataArray[$i][1],
                    $dataArray[0][2] => str_replace(',', '', $dataArray[$i][2]),
                    'slno' => $dataArray[$i][3],
                    'remarks' => $dataArray[$i][4],
                    'company_id' => session('logged_session_data.company_id'),
                    'currency_id' => 1,
                    'import_date' => $request->import_date,

                ];
                //}
                //$data2[]=$data;

            }

            //first delete previous data
            SysItemStockImport::where('company_id', session('logged_session_data.company_id'))->delete();

            SysItemStockImport::insert($data);
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

    public function item_store_import_clear(Request $request)
    {
        try {
            SysItemStockImport::where('company_id', session('logged_session_data.company_id'))->delete();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function item_store_import_data(Request $request)
    {
        try {
           
            DB::beginTransaction();
            $part_number = DB::table('sm_items')->where('status', 1)->pluck('part_number');
            $part_number_id = DB::table('sm_items')->select('id', 'part_number')->where('status', 1)->get();

            $data = DB::table('sys_item_stock_import as i')->select('i.*')->where('i.status', 1)->where('i.company_id', session('logged_session_data.company_id'))
                ->whereIn('i.partno', $part_number)->get();


            $account_id = SysHelper::get_opening_stock_account_id();
            if ($account_id == 0) {
                Toastr::error('Opening Stock Account Not found.', 'Failed');
                return redirect()->back();
            }

            if (count($data) > 0) {

                $ios = new SysItemOpeningStock();
                $ios->doc_number = SysHelper::get_new_code('sys_item_opening_stock', 'OP', 'doc_number');
                $ios->doc_date = $data[0]->import_date;
                $ios->bill_date = $data[0]->import_date;
                $ios->currency = 1;
                $ios->narration = 'excel import';
                $ios->status = 1;
                //$ios->company_id = session('logged_session_data.company_id');
                $ios->company_id = $data[0]->company_id;
                $ios->created_by = Auth::user()->id;
                $ios->created_at = Carbon::now('+04:00');
                $ios->save();
                $ios->toArray();

                foreach ($data as $dt) {
                    $partno = $part_number_id->where('part_number', $dt->partno)->max('id');
                    $inData[] = [
                        'doc_number' => $ios->doc_number,
                        'doc_date' => $ios->doc_date,
                        'ops_id' => $ios->id,
                        'account_id' => $account_id,
                        'partno' => $partno,
                        'description' => SysHelper::get_product_description(null,$partno),
                        'slno' => $dt->slno,
                        'qty_in' => $dt->qty_in,
                        'price_in' => $dt->price_in,
                        'refno' => $dt->slno,
                        'remarks' => $dt->remarks,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                        'company_id' => $dt->company_id,
                        'currency_id' => 1,
                    ];
                }
                //return $inData;
                SysItemStock::insert($inData);

                $amount_dr = $data->sum('price_in');
                $amount_cr = '0.00';
                SysHelper::trn_chartof_accounts_transaction($account_id, $ios->id, $ios->doc_number, $ios->doc_date, 'openingstock', $amount_dr, $amount_cr, 'excel import', 1, 0, "", 0);

                SysItemStockImport::where('company_id', session('logged_session_data.company_id'))->delete();
                DB::commit();
            } else {
                Toastr::error('No Items Found', 'Failed');
                return redirect()->back();
            }
            Toastr::success('Opening Stock Imported Successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    // import

    function productMerge(Request $request)
    {
        try {
            DB::beginTransaction();

            $from_partno = $request->from_partno;
            $to_partno = $request->to_partno;

            $to_part_no_det = DB::table('sm_items')->select('id', 'part_number')->where('id', $to_partno)->get();

            if (count($from_partno) > 0) {
                foreach ($from_partno as $f_partno) {
                    $from_part_no_det = DB::table('sm_items')->select('id', 'part_number')->where('id', $f_partno)->get();

                    $check_from = DB::table('sm_items')->where('part_number', $from_part_no_det[0]->part_number)->count();
                    $check_to = DB::table('sm_items')->where('part_number', $to_part_no_det[0]->part_number)->count();

                    // if($check_to > 1){
                    //     DB::rollBack();
                    //     Toastr::error('Operation Failed. Multipple Part Number', 'Failed');
                    //     return redirect()->back();
                    // }
                    // if($check_from > 1){
                    //     DB::rollBack();
                    //     Toastr::error('Operation Failed. Multipple Part Number', 'Failed');
                    //     return redirect()->back();                        
                    // }

                    //DB::table('sm_items')->where('account_id',$f_partno)->update(['account_id' => $to_partno]);

                    DB::table('sys_item_stock')->where('partno', $f_partno)->update(['partno' => $to_partno]);
                    DB::table('sys_crm_quote_items')->where('product_id', $f_partno)->update(['product_id' => $to_partno]);
                    DB::table('sys_proforma_invoice_items')->where('part_number', $f_partno)->update(['part_number' => $to_partno]);
                    DB::table('sys_sales_invoice_items')->where('part_number', $f_partno)->update(['part_number' => $to_partno]);
                    DB::table('sys_delivery_note_items')->where('part_number', $f_partno)->update(['part_number' => $to_partno]);
                    DB::table('sys_delivery_note_items_srl')->where('part_number', $f_partno)->update(['part_number' => $to_partno]);
                    DB::table('sys_sales_return_list')->where('part_number', $f_partno)->update(['part_number' => $to_partno]);
                    DB::table('sys_sales_return_list_srl')->where('part_number', $f_partno)->update(['part_number' => $to_partno]);
                    DB::table('sys_deal_purchase_order_items')->where('part_number', $f_partno)->update(['part_number' => $to_partno, 'part_number_txt' => $to_partno]);
                    DB::table('sys_purchase_order_items')->where('part_number', $f_partno)->update(['part_number' => $to_partno]);
                    DB::table('sys_purchase_grn_items')->where('part_no', $f_partno)->update(['part_no' => $to_partno, 'part_number' => $to_part_no_det[0]->part_number]);
                    DB::table('sys_purchase_grn_items_srlno')->where('part_no', $f_partno)->update(['part_no' => $to_partno]);
                    //DB::table('sys_purchase_grn_license_key')->where('account_id',$f_partno)->update(['account_id' => $to_partno]);
                    DB::table('sys_purchase_invoice_items')->where('part_number', $f_partno)->update(['part_number' => $to_partno]);
                    DB::table('sys_purchase_return_list')->where('partno', $f_partno)->update(['partno' => $to_partno]);
                    DB::table('sys_purchase_return_items_srlno')->where('part_no', $f_partno)->update(['part_no' => $to_partno]);

                    DB::table('sm_items')->where('id', $f_partno)->update(['status' => 0]);

                    DB::table('sys_items_merge')->insert(['from_id' => $f_partno, 'to_id' => $to_partno, 'status' => 1, 'created_by' => Auth::user()->id]);

                }
            }
            DB::commit();
            Toastr::success('Product Merged Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    function productMergeDuplicate(Request $request)
    {
        try {
            DB::beginTransaction();

            if (count($request->dup_part_no) > 0) {
                foreach ($request->dup_part_no as $key) {
                    $part_no_det = DB::table('sm_items')->select('id', 'part_number')->where('status', 1)->where('part_number', $key)->get();
                    if (count($part_no_det) > 0) {
                        $to_partno = $part_no_det[0]->id;
                        $to_part_no_det = DB::table('sm_items')->select('id', 'part_number')->where('status', 1)->where('id', $to_partno)->get();
                        for ($i = 1; $i < count($part_no_det); $i++) {

                            $f_partno = $part_no_det[$i]->id;

                            DB::table('sys_item_stock')->where('partno', $f_partno)->update(['partno' => $to_partno]);
                            DB::table('sys_crm_quote_items')->where('product_id', $f_partno)->update(['product_id' => $to_partno]);
                            DB::table('sys_proforma_invoice_items')->where('part_number', $f_partno)->update(['part_number' => $to_partno]);
                            DB::table('sys_sales_invoice_items')->where('part_number', $f_partno)->update(['part_number' => $to_partno]);
                            DB::table('sys_delivery_note_items')->where('part_number', $f_partno)->update(['part_number' => $to_partno]);
                            DB::table('sys_delivery_note_items_srl')->where('part_number', $f_partno)->update(['part_number' => $to_partno]);
                            DB::table('sys_sales_return_list')->where('part_number', $f_partno)->update(['part_number' => $to_partno]);
                            DB::table('sys_sales_return_list_srl')->where('part_number', $f_partno)->update(['part_number' => $to_partno]);
                            DB::table('sys_deal_purchase_order_items')->where('part_number', $f_partno)->update(['part_number' => $to_partno, 'part_number_txt' => $to_partno]);
                            DB::table('sys_purchase_order_items')->where('part_number', $f_partno)->update(['part_number' => $to_partno]);
                            DB::table('sys_purchase_grn_items')->where('part_no', $f_partno)->update(['part_no' => $to_partno, 'part_number' => $to_part_no_det[0]->part_number]);
                            DB::table('sys_purchase_grn_items_srlno')->where('part_no', $f_partno)->update(['part_no' => $to_partno]);
                            //DB::table('sys_purchase_grn_license_key')->where('account_id',$f_partno)->update(['account_id' => $to_partno]);
                            DB::table('sys_purchase_invoice_items')->where('part_number', $f_partno)->update(['part_number' => $to_partno]);
                            DB::table('sys_purchase_return_list')->where('partno', $f_partno)->update(['partno' => $to_partno]);
                            DB::table('sys_purchase_return_items_srlno')->where('part_no', $f_partno)->update(['part_no' => $to_partno]);

                            DB::table('sm_items')->where('id', $f_partno)->update(['status' => 0]);

                            DB::table('sys_items_merge')->insert(['from_id' => $f_partno, 'to_id' => $to_partno, 'status' => 1, 'created_by' => Auth::user()->id]);

                        }
                    }

                }
            }

            DB::commit();
            Toastr::success('Product Merged Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    //OPS LICENSE KEY

    /**
     * Store Reserve Stock Data
     */
    public function storeReserveStock(Request $request)
    {




        try {
            DB::beginTransaction();

            // Convert date from d/m/Y to Y-m-d format
            $reserveDate = Carbon::createFromFormat('d/m/Y', $request->reserve_date)->format('Y-m-d');

            // Check if there's enough stock available
            $stockId = $request->reserve_stock_id;
            $requestedQty = $request->reserve_qty;




            // Create reserve stock record
            $reserveStock = new ReserveStock();
            $reserveStock->stock_id = $stockId;
            $reserveStock->deal_id = $request->reserve_deal_id ? SysHelper::get_dealid_from_code($request->reserve_deal_id) : null;
            $reserveStock->part_number = $request->reserve_part_number;
            $reserveStock->customer_id = $request->reserve_customer_id;
            $reserveStock->customer_name = SysCustSuppl::find($request->reserve_customer_id)->customer_name_display ?? 'N/A';
            $reserveStock->sales_person_id = $request->reserve_sales_person ?: null;
            $reserveStock->reserve_qty = $requestedQty;
            $reserveStock->reserve_date = $reserveDate;
            $reserveStock->company_id = session('logged_session_data.company_id');
            $reserveStock->created_by = Auth::id();
            $reserveStock->save();

            DB::commit();

            Toastr::success('Stock reserved successfully!', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Failed to reserve stock: ' . $e->getMessage(), 'Error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Get Reserved Quantity for AJAX call
     */
    public function getReservedQty(Request $request)
    {
        try {
            $stockId = $request->stock_id;
            $partNumber = $request->part_number;
            $excludeId = $request->exclude_id; // ID to exclude from calculation
            $companyId = session('logged_session_data.company_id');

            if (!$stockId) {
                return response()->json(['error' => 'Stock ID is required'], 400);
            }

            // If exclude_id is provided, calculate reserved qty excluding that record
            if ($excludeId) {
                $reservedQty = ReserveStock::where('stock_id', $stockId)
                    ->where('part_number', $partNumber)
                    ->where('company_id', $companyId)
                    ->where('id', '!=', $excludeId)
                    ->whereNull('deleted_at')
                    ->sum('reserve_qty');
            } else {
                $reservedQty = SysHelper::get_reserved_qty($stockId, $partNumber, $companyId);
            }

            return response()->json([
                'success' => true,
                'reserved_qty' => $reservedQty
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to get reserved quantity',
                'reserved_qty' => 0
            ], 500);
        }
    }

    /**
     * Get list of reserved stock for a specific item
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReservedStockList(Request $request)
    {
        try {
            $stockId = $request->stock_id;
            $partNumber = $request->part_number;
            $companyId = session('logged_session_data.company_id');

            if (!$stockId) {
                return response()->json(['error' => 'Stock ID is required'], 400);
            }

            $reservedStocks = ReserveStock::with(['salesPerson'])
                ->where('stock_id', $stockId)
                ->where('part_number', $partNumber)
                ->where('company_id', $companyId)
                ->whereNull('deleted_at')
                ->orderBy('reserve_date', 'asc')
                ->get();

            $stockData = [];
            foreach ($reservedStocks as $stock) {
                $stockData[] = [
                    'doc_number' => $stock->doc_number,
                    'id' => $stock->id,
                    'deal_id' => $stock->deal_id ? SysHelper::get_code_from_dealid($stock->deal_id) : null,
                    'customer_name' => $stock->customer_name,
                    'sales_person' => $stock->salesPerson ? $stock->salesPerson->first_name.' '.$stock->salesPerson->last_name : 'N/A',
                    'reserved_qty' => $stock->reserve_qty,
                    'reserve_date' => Carbon::parse($stock->reserve_date)->format('d/m/Y'),
                    'created_at' => $stock->created_at->format('d/m/Y  h:i A'),
                    'created_by' => $stock->createdBy ? $stock->createdBy->full_name : 'N/A',
                    'updated_at' => $stock->updated_at ? $stock->updated_at->format('d/m/Y  h:i A') : null,
                    'updated_by' => $stock->updatedBy ? $stock->updatedBy->full_name : $stock->createdBy->full_name,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $stockData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to get reserved stock list',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a reserved stock record
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteReservedStock(Request $request)
    {
        try {
            $reserveId = $request->reserve_id;
            $companyId = session('logged_session_data.company_id');

            Log::info('Delete Reserved Stock Request', [
                'reserve_id' => $reserveId,
                'company_id' => $companyId,
                'request_all' => $request->all()
            ]);

            if (!$reserveId) {
                return response()->json(['error' => 'Reserve ID is required'], 400);
            }

            $reserveStock = ReserveStock::where('id', $reserveId)
                ->where('company_id', $companyId)
                ->first();

            if (!$reserveStock) {
                return response()->json([
                    'success' => false,
                    'error' => 'Reserved stock record not found'
                ], 404);
            }

            // Soft delete the record
            $reserveStock->delete();

            return response()->json([
                'success' => true,
                'message' => 'Reserved stock deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete reserved stock',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a single reserved stock record for editing
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReservedStockDetail(Request $request)
    {
        try {
            $reserveId = $request->reserve_id;
            $companyId = session('logged_session_data.company_id');

            if (!$reserveId) {
                return response()->json(['error' => 'Reserve ID is required'], 400);
            }

            $reserveStock = ReserveStock::with(['salesPerson'])
                ->where('id', $reserveId)
                ->where('company_id', $companyId)
                ->whereNull('deleted_at')
                ->first();

            if (!$reserveStock) {
                return response()->json([
                    'success' => false,
                    'error' => 'Reserved stock record not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'doc_number' => $reserveStock->doc_number,
                    'id' => $reserveStock->id,
                    'deal_id' => $reserveStock->deal_id ? SysHelper::get_code_from_dealid($reserveStock->deal_id) : null,
                    'customer_name' => $reserveStock->customer_name,
                    'sales_person_id' => $reserveStock->sales_person_id,
                    'reserve_qty' => $reserveStock->reserve_qty,
                    'customer_id' => $reserveStock->customer_id,
                    'reserve_date' => Carbon::parse($reserveStock->reserve_date)->format('Y-m-d'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to get reserved stock detail',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a reserved stock record
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateReservedStock(Request $request)
    {
        // dd($request->all());

        try {
            $companyId = session('logged_session_data.company_id');
            $userId = session('logged_session_data.user_id');


            $reserveStock = ReserveStock::where('id', $request->reserve_id)
                ->where('company_id', $companyId)
                ->whereNull('deleted_at')
                ->first();

            if (!$reserveStock) {
                Toastr::error('Reserved stock record not found', 'Error');
                return redirect()->back();
            }


            if ($request->has('release_btn') && $request->release_btn === 'release_stock') {

           

                // Delete expired reserve stock records (soft delete)
                $reserveStock->update([
                    'updated_by' => Auth::user()->id,
                    'delivered' => 1
                ]);

                $reserveStock->delete();
                // Soft delete the record

                Toastr::success('Reserved stock released successfully', 'Success');

                return redirect()->back();

                // return redirect()->back()
                //     ->with('success', 'Reserved stock released successfully');

            }



            // Update the record
            $reserveStock->update([
                'customer_name' => SysCustSuppl::find($request->edit_customer_id)->customer_name_display ?? 'N/A',
                'customer_id' => $request->edit_customer_id,
                'sales_person_id' => $request->sales_person_id,
                'reserve_qty' => $request->reserve_qty,
                'reserve_date' => SysHelper::normalizeToYmd($request->reserve_date),
                'updated_by' => $userId
            ]);

            Toastr::success('Reserved stock updated successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            Log::error('Error updating reserved stock: ' . $e->getMessage());
            Toastr::error('An error occurred while updating reserved stock', 'Error');
            return redirect()->back();
        }
    }


    public function cleanupExpiredReserveStock()
    {
        try {
            $today = Carbon::now()->startOfDay();

            // Get count of expired records before deletion
            $expiredCount = ReserveStock::where('reserve_date', '<', $today)
                ->whereNull('deleted_at')
                ->count();

            if ($expiredCount == 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'No expired reserve stock records found',
                    'deleted_count' => 0
                ]);
            }

            // Delete expired reserve stock records (soft delete)
            $deletedCount = ReserveStock::where('reserve_date', '<', $today)
                ->whereNull('deleted_at')
                ->update([
                    'deleted_at' => Carbon::now(),
                    'deleted_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id
                ]);

            Log::info('Manual cleanup of expired reserve stock completed', [
                'deleted_count' => $deletedCount,
                'executed_by' => Auth::user()->id,
                'executed_at' => Carbon::now()
            ]);

            // return response()->json([
            //     'success' => true,
            //     'message' => "Successfully deleted {$deletedCount} expired reserve stock record(s)",
            //     'deleted_count' => $deletedCount
            // ]);

        } catch (\Exception $e) {
            Log::error('Error in manual cleanup of expired reserve stock: ' . $e->getMessage());
            // return response()->json([
            //     'success' => false,
            //     'message' => 'An error occurred while cleaning up expired reserve stock',
            //     'error' => $e->getMessage()
            // ], 500);
        }
    }


    public function getDealIdByCode(Request $request)
    {
        $deal_code = $request->input('deal_code');
        $part_number_id = $request->input('part_number_id');


        $deal = DB::table('sys_crm_deals')
            ->where('code', $deal_code)
            ->orderBy('id', 'desc') // same logic as max(id)
            ->first();

        if (!$deal) {
            return response()->json(['deal_id' => null]);
        }


        $existingItem = SysCrmQuoteItems::where('deal_id', $deal->id)
            ->where('product_id', $part_number_id)
            ->select('id', 'qty', 'price', 'discount')
            ->first();

        if ($existingItem) {
            return response()->json([
                'deal_id' => $deal->id,
                'item_exists' => true,
                'item_details' => [
                    'id' => $existingItem->id,
                    'sales_person' => $deal->owner,
                    'customer_id' => $deal->cust_id,
                    'qty' => $existingItem->qty,
                    'price' => $existingItem->price,
                    'discount' => $existingItem->discount,
                ],
            ]);
        }

        return response()->json(['deal_id' => $deal, 'item_exists' => false]);
    }

    public function item_store_import_delete(Request $request, $id)
    {
        try {
            SysItemStockImport::where('id', $id)->delete();
            Toastr::success('Record deleted successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

}

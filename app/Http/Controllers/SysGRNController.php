<?php

namespace App\Http\Controllers;

use App\SmItem;
use App\SmStaff;
use App\SmSupplier;
use App\SysCompany;
use App\SysPurchaseOrder;
use App\SysPurchaseOrderItems;
use App\SysPurchaseOrderAttachment;
use App\SmQuotation;
use App\SysCurrencySettings;
use App\SysPaymentTerms;
use App\SysShipping;
use App\SmGeneralSettings;
use App\SmQuotationProducts;
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
use PHPExcel;
use PHPExcel_IOFactory;


use App\Role;
use App\SysChartofAccounts;
use App\SysCountries;
use App\SysCrmDeals;
use App\SysCurrency;
use App\SysCurrencyRate;
use App\SysCustSuppl;
use App\SysCustSupplAddressbook;
use App\SysGRNItemsCart;
use App\SysHelper;
use App\SysItemStock;
use App\SysPurchaseDlnLicenseKey;
use App\SysPurchaseGRN;
use App\SysPurchaseGRNItems;
use App\SysPurchaseGRNItemsSrlno;
use App\SysPurchaseGRNItemsSrlnoCart;
use App\SysPurchaseGrnLicenseKey;
use App\SysPurchaseInvoice;
use App\SysPurchaseInvoiceCFCharges;
use App\SysPurchaseOrderItemsCart;
use App\SysPurchaseType;
use App\SysStates;
use App\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Hash;
use Nexmo\Numbers\Number;
use App\SysPurchaseGrnCfCharges;

use function GuzzleHttp\Promise\exception_for;


class SysGRNController extends Controller
{
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
            $supplier_list = SysHelper::get_supplier_list($company_id);

            $query = SysPurchaseGRN::select(DB::raw('sys_purchase_grn.*, (SELECT GROUP_CONCAT(doc_file) FROM sys_purchase_order_att WHERE doc_id = sys_purchase_grn.po_id) AS attach, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_invoice WHERE ref_grn_id=sys_purchase_grn.id) AS piv_no, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_return WHERE ref_grn_id=sys_purchase_grn.id) AS prt_no, (SELECT SUM(taxableamount)+SUM(vatamount) AS amount FROM sys_purchase_grn_items WHERE grn_id=sys_purchase_grn.id) AS amount, sys_purchase_grn.deal_id AS code'));

            if (SysHelper::get_pagination_post($request)) {
                if ($request->documents_number != "") {
                    $query->where('doc_number', 'like', '%' . $request->documents_number . '%');
                }
                if ($request->supplier != "") {
                    $query->where('vendors', $request->supplier);
                }
                if ($request->customer != "") {
                    $query->where('narration', 'like', '%' . $request->customer . '%');
                }
                if ($request->deal_number != "") {
                    $query->where('deal_id', 'like', '%' . SysHelper::get_dealid_from_code($request->deal_number) . '%');
                }
                if ($request->purchase_invoice_number != "") {
                    $inv_nos = SysPurchaseGRN::join('sys_purchase_invoice', 'sys_purchase_invoice.ref_grn_id', 'sys_purchase_grn.id')
                        ->where('sys_purchase_invoice.doc_number', 'like', '%' . $request->purchase_invoice_number . '%')->pluck('sys_purchase_grn.doc_number');
                    $query->wherein('doc_number', $inv_nos);
                }
                if ($request->purchase_order_number != "") {
                    $po_nos = SysPurchaseGRN::join('sys_purchase_order', 'sys_purchase_order.id', 'sys_purchase_grn.po_id')
                        ->where('sys_purchase_order.doc_number', 'like', '%' . $request->purchase_order_number . '%')->pluck('sys_purchase_grn.doc_number');
                    $query->wherein('doc_number', $po_nos);
                }
                if ($request->purchase_return_number != "") {
                    $prt_nos = SysPurchaseGRN::join('sys_purchase_return', 'sys_purchase_return.ref_grn_id', 'sys_purchase_grn.id')
                        ->where('sys_purchase_return.doc_number', 'like', '%' . $request->purchase_return_number . '%')->pluck('sys_purchase_grn.doc_number');
                    $query->wherein('doc_number', $prt_nos);
                }
                // if ($request->amount != "") {                    
                //     $amt_nos = SysChartofAccountsTransaction::where('transaction_type', 'salesreturn')->where('debit_amount',$request->amount)->pluck('transaction_no');
                //     $query->wherein('doc_number',$amt_nos);
                // }
                if ($request->date != "") {
                    $query->where('doc_date', $request->date);
                }
            } else {
            }
            $query->wherein('company_id', $company_id);
            $query->orderby('doc_number', 'desc');
            $purchasegrn = $query->paginate(50);
            //return $purchaseorder;
            return view('backEnd.grn.grn_invoice_list', compact('purchasegrn', 'supplier_list'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
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
            $com_id = SysHelper::get_company_access();
            $id = null;



            if ($company_id == 1) {
                $query = SysPurchaseGRN::select(DB::raw('sys_purchase_grn.*, (SELECT GROUP_CONCAT(doc_file) FROM sys_purchase_order_att WHERE doc_id = sys_purchase_grn.po_id) AS attach, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_invoice WHERE ref_grn_id=sys_purchase_grn.id) AS piv_no, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_return WHERE ref_grn_id=sys_purchase_grn.id) AS prt_no, (SELECT SUM(taxableamount)+SUM(vatamount) AS amount FROM sys_purchase_grn_items WHERE grn_id=sys_purchase_grn.id) AS amount, sys_purchase_grn.deal_id AS code'));
                $query->orderby('id', 'desc');
                $purchasegrn = $query->get();
            } else {
                $query = SysPurchaseGRN::select(DB::raw('sys_purchase_grn.*, (SELECT GROUP_CONCAT(doc_file) FROM sys_purchase_order_att WHERE doc_id = sys_purchase_grn.po_id) AS attach, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_invoice WHERE ref_grn_id=sys_purchase_grn.id) AS piv_no, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_return WHERE ref_grn_id=sys_purchase_grn.id) AS prt_no, (SELECT SUM(taxableamount)+SUM(vatamount) AS amount FROM sys_purchase_grn_items WHERE grn_id=sys_purchase_grn.id) AS amount, sys_purchase_grn.deal_id AS code'));
                $query->where('company_id', $company_id);
                $query->orderby('id', 'desc');
                $purchasegrn = $query->get();
            }
            if (count($purchasegrn) > 0) {
                $id = $purchasegrn->first()->id;
            }




            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $salesman = SysHelper::get_sales_persons();
            $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();
            $vendors = SysHelper::get_supplier_list($company_id);
            $items = SysHelper::get_product_list($company_id);
            //return $items;

            $staff = SysHelper::get_staff_list();

            //SysPurchaseGrnLicenseKey::

            $departments = SmInspectingDepartment::all();
            $shipping = SysShipping::all();
            $suppliertype = SysSupplierType::orderby('title', 'asc')->get();
            $purchasetype = SysPurchaseType::orderby('title', 'asc')->get();
            $countries = SysCountries::orderby('name', 'asc')->get();
            $states = SysStates::orderby('name', 'asc')->get();

            $customer = SysHelper::get_customer_supplier_list($company_id);
            $customer_reference_list = SysHelper::get_customer_list_deal_lead_all_role();



            return compact('purchasegrn', 'currency', 'vendors', 'items', 'departments', 'paymentterms', 'company', 'shipping', 'suppliertype', 'purchasetype', 'countries', 'staff', 'salesman', 'customer', 'states', 'customer_reference_list');
            // return view('backEnd.grn.manage_grn_invoice', compact('purchasegrn', 'currency', 'vendors', 'items', 'departments', 'paymentterms', 'company', 'shipping', 'suppliertype', 'purchasetype', 'countries', 'staff', 'salesman'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function goodsreceiptnotepending(Request $request)
    {
        try {
            $ret = SysPurchaseOrder::select('id', 'doc_number')->where('vendors', $request->id)->where('company_id', session('logged_session_data.company_id'))->where('grn_status', 0)->where('status', 1)->orderby('id', 'desc')->get();
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

    function goodsreceiptnotependingitemlist(Request $request)
    {
        try {
            $poid = [];
            $newArray = array_map('intval', explode(',', $request->po_id));
            if (count($newArray) > 0) {
                foreach ($newArray as $key) {
                    $poid[] = array($key);
                }
            }
            $ret = SysPurchaseOrderItems::select(
                'sys_purchase_order_items.id',
                'sys_purchase_order_items.po_id',
                'sys_purchase_order.po_date',
                'sys_purchase_order.doc_number',
                'sys_purchase_order.created_by',
                'sys_purchase_order.payment_terms',
                'sys_purchase_order.currency',
                'sys_purchase_order.narration',
                // 'sm_staffs.user_id',
                'sys_purchase_order.sales_person_name',
                'sys_purchase_order.sales_person',
                'sys_purchase_order.deal_id',
                'deal.code',
                'sys_purchase_order.company_id',
                'sys_purchase_order.ref_company_id',
                'itm.id AS part_id',
                'itm.part_number',
                'itm.hscode',
                'sys_purchase_order_items.qty AS po_qty',
                'sys_purchase_order_items.grn_qty',
                'sys_purchase_order_items.issue_qty',
                'sys_purchase_order_items.unitprice',
                'sys_purchase_order_items.value',
                'sys_purchase_order_items.discount',
                'sys_purchase_order_items.tax',
                'sys_purchase_order_items.taxableamount',
                'sys_purchase_order_items.vatamount',
                'sys_purchase_order_items.fright',
                'sys_purchase_order_items.customcharges',
                'itm.product_type',
                'sys_purchase_order_items.description',
                'sys_purchase_order_items.sort_id',
                // Shipping details from PO
                'sys_purchase_order.shipping_supplier',
                'sys_purchase_order.shipping_name',
                'sys_purchase_order.shipping_email',
                'sys_purchase_order.shipping_contact_no',
                'sys_purchase_order.shipping_address_1',
                'sys_purchase_order.supplier_country',
                'sys_purchase_order.supplier_state',
                'sys_purchase_order.vat_percent',
                'sys_purchase_order.vat_number',
                'sys_purchase_order.supplier_type',
                'sys_purchase_order.purchase_type',
                DB::raw('(SELECT GROUP_CONCAT(srl_no) FROM sys_purchase_order_items_srl WHERE item_id = sys_purchase_order_items.id) as serial_no')
            )
                ->join('sys_purchase_order', 'sys_purchase_order.id', 'sys_purchase_order_items.po_id')
                // ->join('sm_staffs', 'sm_staffs.user_id', 'sys_purchase_order.sales_person')
                ->join('sm_items as itm', 'itm.id', 'sys_purchase_order_items.part_number')
                ->leftjoin('sys_crm_deals as deal', 'deal.id', 'sys_purchase_order.deal_id')
                ->whereColumn('qty', '>', 'grn_qty')->wherein('po_id', $poid)->orderby('sort_id', 'asc')->orderby('id', 'asc')->orderBy('sys_purchase_order.id', 'desc')->get();


            // Group by doc_number, sorted by latest doc_number first
            $ret = $ret->groupBy('doc_number')->sortKeysDesc()->map(function ($group) {
                return $group->values(); // Reindex array
            });




            /*$ret = DB::select("SELECT po.id,itm.id AS part_id,itm.part_number,po.qty AS po_qty,itmstock.qty AS os_qty,
            (SELECT sum(qty) FROM sys_purchase_invoice_items WHERE ref_po_id = po.po_id AND part_number = po.part_number) pro_qty
            FROM sys_purchase_order_items po
            INNER JOIN sm_items itm ON itm.id=po.part_number
            INNER JOIN sys_item_stock itmstock ON itmstock.partno=po.part_number
            WHERE po_id = '". $request->po_id."'");*/

            /*$ret = SysQuotationsItems::select('sys_quotations_items.id','sm_items.part_number','sys_quotations_items.qty as qt_qty','sys_item_stock.qty as os_qty','sys_proforma_invoice_items.qty as pro_qty')
                ->join('sm_items','sm_items.id','sys_quotations_items.part_number')
                ->join('sys_item_stock','sys_item_stock.partno','sys_quotations_items.part_number')
                ->leftjoin('sys_proforma_invoice_items','sys_proforma_invoice_items.ref_qt_id','sys_quotations_items.qt_id')
                ->where('qt_id',$request->qt_id)->get();*/

            return response()->json([$ret]);
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }


    function addserialno(Request $request)
    {
        try {
            $check = SysPurchaseGRNItemsSrlnoCart::where('session_id', session('logged_session_data.cart_id'))->where('part_no', $request->part_no)->where('item_id', $request->item_id)->get();
            if (count($check) >= $request->qty) {
                $ret = 'QTYERROR';
                return json_encode(array('data' => $ret));
            }

            DB::table('sys_purchase_grn_items_srlno_cart')->insert(
                [
                    'session_id' => session('logged_session_data.cart_id'),
                    'po_id' => $request->po_id,
                    'part_no' => $request->part_no,
                    'part_number' => $request->part_number,
                    'srl_no' => $request->srl_no,
                    'item_id' => $request->item_id,
                ]
            );
            $ret = SysPurchaseGRNItemsSrlnoCart::where('session_id', session('logged_session_data.cart_id'))->where('part_no', $request->part_no)->where('item_id', $request->item_id)->get();
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
    function getserialno(Request $request)
    {
        try {
            $ret = SysPurchaseGRNItemsSrlnoCart::where('session_id', session('logged_session_data.cart_id'))->where('part_no', $request->part_no)->where('item_id', $request->item_id)->get();
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

    public function store(Request $request)
    {


        if (!isset($request->part_number)) {
            Toastr::error('Operation Failed. please enter valid data', 'Failed');
            return redirect()->back();
        }

        /*for($i = 0; $i < count($request->part_number); $i++) {
            $qty = $request->qty[$i];
            $grn_qty = $request->grn_qty[$i];
            if($qty > $grn_qty){
                Toastr::error('Operation Failed. please enter valid data', 'Failed');
                return redirect()->back();
            }            
        }*/

        DB::beginTransaction();
        try {
            $grn = new SysPurchaseGRN();
            $grn->doc_number = SysHelper::get_new_code('sys_purchase_grn', 'GR', 'doc_number');
            // $date = DateTime::createFromFormat('d/m/Y', $request->grn_date);

            $grn->grn_date = $request->filled('grn_date')
                ? Carbon::createFromFormat('d/m/Y', $request->grn_date)->format('Y-m-d')
                : null;


            $grn->po_id = empty($request->po_id) ? 0 : $request->po_id;




            $grn->vendors = $request->vendors;
            $grn->currency = $request->currency;
            $grn->lpo_number = $request->lpo_number;

            if ($request->lpo_date) {

                $grn->lpo_date = Carbon::createFromFormat('d/m/Y', $request->lpo_date)->format('Y-m-d');
            }



            $grn->payment_terms = $request->payment_terms;
            $grn->bill_number = $request->bill_number;

            $grn->bill_date = Carbon::createFromFormat('d/m/Y', $request->bill_date)->format('Y-m-d');
            $grn->awbno = $request->awbno;
            $grn->boeno = $request->boeno;
            $grn->warehouse = $request->warehouse;
            $grn->reference = $request->reference;
            $grn->narration = $request->narration;
            $grn->ref_company_id = isset($request->ref_company_id)
                ? implode(',', $request->ref_company_id)
                : null;
            $grn->deal_id = SysHelper::get_dealid_from_code_list($request->deal_id);

            $sales_person = $request->sales_person;
            if (!is_null($sales_person) && $sales_person !== '') {
                if (is_numeric($sales_person)) {
                    // Selected from dropdown (user ID)
                    $grn->sales_person = (int) $sales_person;
                    $grn->sales_person_name = null;
                } else {
                    // Manually entered name
                    $grn->sales_person = null;
                    $grn->sales_person_name = trim($sales_person);
                }
            }

            // $grn->sales_person = $request->sales_person;

            $grn->status = 1;
            $grn->created_by = Auth::user()->id;
            $grn->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $grn->company_id = session('logged_session_data.company_id');



            $grn->shipping_supplier = $request->shipping_supplier;
            $grn->shipping_name = $request->shipping_name;
            $grn->shipping_email = $request->shipping_email;
            $grn->shipping_contact_no = $request->shipping_contact_no;
            $grn->shipping_address_1 = $request->shipping_address_1;
            $grn->supplier_country = $request->supplier_country;
            $grn->supplier_state = $request->supplier_state;
            $grn->vat_percent = $request->vat_percent;
            $grn->vat_number = $request->vat_number;
            $grn->supplier_type = $request->supplier_type;
            $grn->purchase_type = $request->purchase_type;








            $grn->save();





            for ($i = 0; $i < count($request->part_number); $i++) {
                if ($request->qty[$i] != "" && $request->qty[$i] > 0) { //$request->qty[$i] <= $request->grn_qty[$i] &&
                    $tax = $request->tax[$i] === "" ? 0 : $request->tax[$i];
                    $grnitms = new SysPurchaseGRNItems();
                    $grnitms->grn_id = $grn->id;

                    if (isset($request->list_po_id[$i])) {
                        $grnitms->po_id = $request->list_po_id[$i] ?: 0;
                    } else {
                        $grnitms->po_id = 0;
                    }
                    $grnitms->part_no = $request->part_number[$i];
                    $grnitms->part_number = $request->part_number_txt[$i];
                    $grnitms->description = $request->description[$i];
                    $grnitms->hscode = $request->hscode_txt[$i];
                    $grnitms->tax = $tax;
                    $grnitms->qty = $request->qty[$i];
                    $grnitms->unitprice = (float) str_replace(',', '', $request->unitprice[$i] ?? 0);
                    $grnitms->value = (float) str_replace(',', '', $request->value[$i] ?? 0);
                    if ($request->discount[$i] == null) {
                        $discount = 0;
                    } else {
                        $discount = (float) str_replace(',', '', $request->discount[$i] ?? 0);
                    }
                    if ($request->customcharges[$i] == null) {
                        $customcharges = 0;
                    } else {
                        $customcharges = (float) str_replace(',', '', $request->customcharges[$i] ?? 0);
                    }
                    if ($request->fright[$i] == null) {
                        $fright = 0;
                    } else {
                        $fright = (float) str_replace(',', '', $request->fright[$i] ?? 0);
                    }
                    $grnitms->discount = $discount;
                    $grnitms->customcharges = $customcharges;
                    $grnitms->fright = $fright;
                    $value = (float) str_replace(',', '', $request->value[$i] ?? 0);
                    $grnitms->taxableamount = $value - $discount;
                    $grnitms->vatamount = abs(($value - $discount) * $tax / 100);

                    try {
                        $grnitms->taxableamount = $value - $discount + ($customcharges + $fright);
                        $grnitms->vatamount = ($value - $discount + ($customcharges + $fright)) * $request->tax[$i] / 100;
                    } catch (\Throwable $th) {
                        $grnitms->taxableamount = 0;
                        $grnitms->vatamount = 0;
                    }


                    $grnitms->status = 1;
                    try {
                        $grnitms->sort_id = $request->sort_id[$i];
                    } catch (\Throwable $th) {
                        $grnitms->sort_id = $i + 1;
                    }

                    $grnitms->save();



                    $str_arr = explode(",", $request->serial_no[$i]);
                    /*$str_arr = collect(preg_split('/[\s,]+/', $request->srl[$i], -1, PREG_SPLIT_NO_EMPTY))
                    ->map(fn($s) => strtoupper(trim($s)))->unique()->values()->toArray();*/
                    foreach ($str_arr as $srl) {
                        $values = array('grn_id' => $grn->id, 'part_no' => $request->part_number[$i], 'srl_no' => $srl, 'item_id' => $grnitms->id);
                        DB::table('sys_purchase_grn_items_srlno')->insert($values);
                    }

                    // $ret = SysPurchaseGRNItemsSrlnoCart::select('srl_no')->where('session_id',session('logged_session_data.cart_id'))->where('part_no',$request->part_id[$i])->get();
                    // if(count($ret)>0){
                    //     foreach($ret as $sl){
                    //         DB::table('sys_purchase_grn_items_srlno')->insert(
                    //             [
                    //                 'grn_id' => $grn->id,
                    //                 'po_id' => $grn->po_id,
                    //                 'part_no' => $request->part_id[$i],
                    //                 'srl_no' => $sl->srl_no,
                    //             ]
                    //             );
                    //         SysPurchaseGRNItemsSrlnoCart::where('session_id',session('logged_session_data.cart_id'))->where('part_no',$request->part_id[$i])->delete();
                    //     }
                    // }
                    $keyData = SysPurchaseGrnLicenseKey::where('item_id', $request->part_number[$i])->where('grn_id', '-1')->where('type', 1)->where('cart_id', session('logged_session_data.cart_id'))->where('company_id', session('logged_session_data.company_id'))->get();
                    if (count($keyData) > 0) {
                        foreach ($keyData as $k) {
                            SysHelper::set_license_key_trn(1, $grn->id, $grn->grn_date, $grn->doc_number, $k->id, $k->item_id, $k->license_key, $k->exp_date);
                        }
                    }

                    SysPurchaseGrnLicenseKey::where('item_id', $request->part_number[$i])->where('grn_id', '-1')->where('type', 1)->where('cart_id', session('logged_session_data.cart_id'))->where('company_id', session('logged_session_data.company_id'))->update(['grn_id' => $grn->id]);

                    if (isset($request->list_po_id[$i])) {

                        $list_po_id = $request->list_po_id[$i] ?: 0;
                        $po_itm_id = $request->po_itm_id[$i] ?: 0;
                        $grn_quantity = DB::table('sys_purchase_order_items')->where('po_id', $list_po_id)->where('id', $po_itm_id)->where('part_number', $request->part_number[$i])->sum('grn_qty');

                        //  dd($request->list_po_id,$request->po_itm_id);

                        DB::table('sys_purchase_order_items')->where('po_id', $list_po_id)->where('part_number', $request->part_number[$i])->where('id', $po_itm_id)
                            ->update(['grn_qty' => $grn_quantity + $request->qty[$i]]);
                    }

                    $discount = ($request->discount[$i] === '' ? '0.00' : $request->discount[$i]);
                    $istock = new SysItemStock();
                    $istock->grn_id = $grn->id;
                    $istock->account_id = $request->vendors;
                    $istock->partno = $request->part_number[$i];
                    $istock->qty_in = $request->qty[$i];
                    $istock->price_in = ($value - $discount) / $request->qty[$i];

                    $istock->refno = $grn->lpo_number;
                    $istock->doc_number = $grn->doc_number;
                    $istock->doc_date = $grn->grn_date;
                    $istock->deal_id = $grn->deal_id;
                    $istock->slno = $request->serial_no[$i];
                    $istock->status = 1;
                    $istock->created_by = Auth::user()->id;
                    $istock->company_id = session('logged_session_data.company_id');
                    $istock->currency_id = $request->currency;


                    $sales_person = $request->sales_person;
                    if (!is_null($sales_person) && $sales_person !== '') {
                        if (is_numeric($sales_person)) {
                            // Selected from dropdown (user ID)
                            $istock->sales_person = (int) $sales_person;
                        } else {
                            // Manually entered name
                            $istock->sales_person = null;
                        }
                    }

                    // $istock->sales_person = $request->sales_person;


                    $istock->item_id = $grnitms->id;
                    $istock->save();
                }
            }
            SysPurchaseGrnLicenseKey::where('grn_id', 0)->where('type', 1)->where('cart_id', session('logged_session_data.cart_id'))
                ->where('company_id', session('logged_session_data.company_id'))->delete();

            if (isset($request->list_po_id)) {
                for ($a = 0; $a < count($request->list_po_id); $a++) {
                    $list_po_id = $request->list_po_id[$a] ?: 0;
                    $po = SysPurchaseOrderItems::where('po_id', $list_po_id)->sum('qty');
                    $gr = SysPurchaseGRNItems::where('po_id', $list_po_id)->where('po_id', '!=', 0)->sum('qty');

                    if ($po <= $gr) {
                        DB::table('sys_purchase_order')->where('id', $list_po_id)->update(['grn_status' => 1]);
                    }
                }
            }

       

            if ($request->has('cfc_name') && count($request->cfc_name) > 0) {
             
                
                for($i = 0; $i < count($request->cfc_name); $i++) {
                    if($request->cfc_name[$i] !="" && $request->cfc_credit_account[$i] !="" && $request->cfc_amount[$i] !=""){
                $cfc = new SysPurchaseGrnCfCharges();
                $cfc->grn_id = $grn->id;
                $cfc->date = $request->cfc_date[$i] ? SysHelper::normalizeToYmd($request->cfc_date[$i]) : null;
                $cfc->bill_number = $request->cfc_bill_no[$i];
                $cfc->cfc_name = $request->cfc_name[$i];
                $cfc->cfc_credit_account = $request->cfc_credit_account[$i];
                $cfc->cfc_amount = str_replace(',', '', $request->cfc_amount[$i]);
                $cfc->cfc_remarks = $request->cfc_remarks[$i];
                $cfc->save();
                    }
                }

            }

           

            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect('goods-receipt-note-list/' . $grn->id);
        } catch (\Exception $e) {
            return $e;
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function goodsreceiptnoteforpi(Request $request)
    {
        try {
            $ret = SysPurchaseGRN::where('vendors', $request->id)->where('pi_status', 1)->where('company_id', session('logged_session_data.company_id'))->orderby('id', 'desc')->get();
            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            dd($e);
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }
    function goodsreceiptnoteforpiitemlist(Request $request)
    {
        try {

            $grnid = [];

            $newArray = explode(',', $request->grn_id);

            if (count($newArray) > 0) {
                foreach ($newArray as $key) {
                    $grnid[] = [trim($key)];
                }
            }



            $ret = DB::table('sys_purchase_grn_items as grn_itm')
                ->select([
                    'grn_itm.id',
                    'grn.po_id',
                    'grn.lpo_number',
                    'grn.shipping_supplier',
                    'grn.shipping_name',
                    'grn.shipping_email',
                    'grn.shipping_contact_no',
                    'grn.shipping_address_1',
                    'grn.supplier_country',
                    'grn.supplier_state',
                    'deal.code as deal_code',
                    'grn.lpo_date',
                    'grn.grn_date',
                    'grn.created_by',
                    'grn.payment_terms',
                    'grn.currency',
                    'grn.bill_number',
                    'grn.bill_date',
                    'grn.awbno',
                    'grn.boeno',
                    'grn.warehouse',
                    'grn.doc_number',
                    'grn.deal_id',
                    'grn.ref_company_id',
                    'grn.sales_person',
                    'grn.sales_person_name',
                    'grn.reference',
                    'itm.id as part_id',
                    'itm.part_number',
                    'itm.description',
                    'grn_itm.qty as grn_qty',
                    'grn_itm.unitprice',
                    'grn_itm.discount',
                    'grn_itm.value',
                    'grn_itm.taxableamount',
                    'grn_itm.vatamount',
                    'grn_itm.tax',
                    'grn_itm.grn_id',
                    'po.shipping_address_2',
                    'po.supplier_type',
                    'po.purchase_type',
                    'grn_itm.fright',
                    'grn_itm.customcharges',
                    'grn_itm.sort_id',
                    DB::raw('(SELECT GROUP_CONCAT(srl_no) 
                  FROM sys_purchase_grn_items_srlno 
                  WHERE item_id = grn_itm.id) as serial_no')
                ])
                ->join('sys_purchase_grn as grn', 'grn.id', '=', 'grn_itm.grn_id')
                ->join('sm_items as itm', 'itm.id', '=', 'grn_itm.part_no')
                ->leftJoin('sys_purchase_order as po', 'po.id', '=', 'grn.po_id')
                ->leftJoin('sys_crm_deals as deal', 'deal.id', '=', 'grn.deal_id')
                ->where(function ($query) {
                    $query->whereColumn('grn_itm.qty', '>', 'grn_itm.pi_qty')
                        ->orWhereNull('grn_itm.pi_qty');
                })

                ->whereIn('grn.doc_number', $grnid)
                ->orderBy('grn_itm.sort_id')
                ->get();





            return response()->json([$ret]);
        } catch (\Exception $e) {
            dd($e);
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }
    // function goodsreceiptnoteforpiitemlist(Request $request)
    // {
    //     try {
    //         if ($request->po_id == 0) {
    //             $ret = DB::table('sys_purchase_grn_items as grn_itm')->select('grn_itm.id', 'grn.po_id', 'deal.code as deal_code', 'grn.lpo_number', 'grn.lpo_date', 'grn.grn_date', 'grn.created_by', 'grn.payment_terms', 'grn.currency', 'grn.bill_number', 'grn.bill_date', 'grn.awbno', 'grn.boeno', 'grn.warehouse', 'grn.doc_number', 'grn.grn_date', 'grn.deal_id', 'grn.sales_person', 'grn.reference', 'itm.id AS part_id', 'itm.part_number', 'itm.description', 'grn_itm.qty AS grn_qty', 'grn_itm.unitprice', 'grn_itm.discount', 'grn_itm.value', 'grn_itm.taxableamount', 'grn_itm.vatamount', 'grn_itm.tax', 'cs.contcat_person as shipping_name', 'cs.address as shipping_address_1', 'cs.address2 as shipping_address_2', 'cs.contcat_number as shipping_contact_no', 'cs.vat_type as supplier_type', 'cs.purchase_type as purchase_type', 'cs.vat_country as supplier_country', 'cs.vat_state as supplier_state', 'grn_itm.fright', 'grn_itm.customcharges', 'grn_itm.sort_id', DB::raw('(SELECT GROUP_CONCAT(srl_no) FROM sys_purchase_grn_items_srlno WHERE item_id = grn_itm.id) as serial_no'))
    //                 ->join('sys_purchase_grn as grn', 'grn.id', 'grn_itm.grn_id')
    //                 ->join('sm_items as itm', 'itm.id', 'grn_itm.part_no')
    //                 ->leftjoin('sys_chartofaccounts as ca', 'ca.id', 'grn.vendors')
    //                 ->leftjoin('sys_cust_suppl as cs', 'cs.code', 'ca.account_code')
    //                 ->leftJoin('sys_crm_deals as deal', 'deal.id', '=', 'grn.deal_id')
    //                 ->where('grn.id', $request->grn_id)->get();
    //         } else {
    //             $ret = DB::table('sys_purchase_grn_items as grn_itm')->select('grn_itm.id', 'grn.po_id', 'grn.lpo_number', 'deal.code as deal_code', 'grn.lpo_date', 'grn.grn_date', 'grn.created_by', 'grn.payment_terms', 'grn.currency', 'grn.bill_number', 'grn.bill_date', 'grn.awbno', 'grn.boeno', 'grn.warehouse', 'grn.doc_number', 'grn.grn_date', 'grn.deal_id', 'grn.sales_person', 'grn.reference', 'itm.id AS part_id', 'itm.part_number', 'itm.description', 'grn_itm.qty AS grn_qty', 'grn_itm.unitprice', 'grn_itm.discount', 'grn_itm.value', 'grn_itm.taxableamount', 'grn_itm.vatamount', 'grn_itm.tax', 'po.shipping_name', 'po.shipping_address_1', 'po.shipping_address_2', 'po.shipping_contact_no', 'po.supplier_type', 'po.purchase_type', 'po.supplier_country', 'po.supplier_state', 'grn_itm.fright', 'grn_itm.customcharges', 'grn_itm.sort_id', DB::raw('(SELECT GROUP_CONCAT(srl_no) FROM sys_purchase_grn_items_srlno WHERE item_id = grn_itm.id) as serial_no'))
    //                 ->join('sys_purchase_grn as grn', 'grn.id', 'grn_itm.grn_id')
    //                 ->join('sm_items as itm', 'itm.id', 'grn_itm.part_no')
    //                 ->leftjoin('sys_purchase_order as po', 'po.id', 'grn.po_id')
    //                 ->leftJoin('sys_crm_deals as deal', 'deal.id', '=', 'grn.deal_id')
    //                 ->where('grn.id', $request->grn_id)->orderby('grn_itm.sort_id')->get();
    //         }

    //         return response()->json([$ret]);
    //     } catch (\Exception $e) {
    //         $ret = 'ERROR';
    //         return json_encode(array('data' => $ret));
    //     }
    // }
    function get_deal_code_from_id(Request $request)
    {
        $ret = SysHelper::get_code_from_dealid_list($request->deal_id);
        return response()->json([$ret]);
    }
    function remove_grn_items(Request $request)
    {
        try {
            DB::table('sys_purchase_grn_items')->where('grn_id', $request->id)->delete();
            $ret = 'SUCCESS';
            return json_encode(array('data' => $ret));
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }


    function addgrnitemscart(Request $request)
    {
        try {
            DB::table('sys_grn_items_cart')->insert([
                'cart_id' => session('logged_session_data.cart_id'),
                'part_number' => $request->part_number,
                'tax' => $request->tax,
                'qty' => $request->qty,
                'unitprice' => $request->unitprice,
                'value' => $request->value,
                'discount' => $request->discount,
                'customcharges' => $request->customcharges,
                'taxableamount' => $request->taxableamount,
                'vatamount' => $request->vatamount,
                'status' => 1,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now('+04:00'),
            ]);

            $ret = SysGRNItemsCart::select('sys_grn_items_cart.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_grn_items_cart.part_number')
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

    function deletegrnitemscart(Request $request)
    {
        try {
            DB::table('sys_grn_items_cart')->where('id', $request->id)->delete();
            $ret = SysGRNItemsCart::select('sys_grn_items_cart.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_grn_items_cart.part_number')
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

    // public function edit(Request $request, $id)
    // {
    //     try {
    //         $grn_select_id = $id;
    //         $r = SysHelper::get_data_by_role();
    //         $company_id = $r[0];
    //         if ($company_id == 1) {
    //             $query = SysPurchaseGRN::select(DB::raw('sys_purchase_grn.*, (SELECT GROUP_CONCAT(doc_file) FROM sys_purchase_order_att WHERE doc_id = sys_purchase_grn.po_id) AS attach, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_invoice WHERE ref_grn_id=sys_purchase_grn.id) AS piv_no, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_return WHERE ref_grn_id=sys_purchase_grn.id) AS prt_no, (SELECT SUM(taxableamount)+SUM(vatamount) AS amount FROM sys_purchase_grn_items WHERE grn_id=sys_purchase_grn.id) AS amount, sys_purchase_grn.deal_id AS code'));
    //             $query->orderby('id', 'desc');
    //             $purchasegrn = $query->get();
    //         } else {
    //             $query = SysPurchaseGRN::select(DB::raw('sys_purchase_grn.*, (SELECT GROUP_CONCAT(doc_file) FROM sys_purchase_order_att WHERE doc_id = sys_purchase_grn.po_id) AS attach, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_invoice WHERE ref_grn_id=sys_purchase_grn.id) AS piv_no, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_return WHERE ref_grn_id=sys_purchase_grn.id) AS prt_no, (SELECT SUM(taxableamount)+SUM(vatamount) AS amount FROM sys_purchase_grn_items WHERE grn_id=sys_purchase_grn.id) AS amount, sys_purchase_grn.deal_id AS code'));
    //             $query->where('company_id', $company_id);
    //             $query->orderby('id', 'desc');
    //             $purchasegrn = $query->get();
    //         }

    //         $currency       = SysCurrencySettings::select('id', 'code', 'ex_rate')->get();
    //         $company        = SysCompany::find(session('logged_session_data.company_id'));
    //         $salesman = SysHelper::get_sales_persons();
    //         $paymentterms   = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();

    //         $items = SysHelper::get_product_list($company_id);

    //         $grn = SysPurchaseGRN::where('id', $id)->first();


    //         $vendors = SysChartofAccounts::select('id', 'account_name')->where('id', $grn->vendors)->get();

    //         $currencylist2 = DB::table('sys_currency_rate as r')->select('r.id', 'r.from_currency', 'r.to_currency', 'c.code', 'r.rate')
    //             ->join('sys_currency as c', 'c.id', 'r.to_currency')
    //             ->where('r.status', 1)->where('r.from_currency', $grn->currency)
    //             ->orderBy('c.code', 'ASC')->get();

    //         $staff = SysHelper::get_staff_list();

    //         $departments    = SmInspectingDepartment::all();
    //         $shipping    = SysShipping::all();
    //         $suppliertype = SysSupplierType::orderby('title', 'asc')->get();
    //         $purchasetype = SysPurchaseType::orderby('title', 'asc')->get();
    //         $countries = SysCountries::orderby('name', 'asc')->get();
    //         $states = SysStates::orderby('name', 'asc')->get();

    //         if ($grn->po_id == 0) {
    //             $grn_items = DB::table('sys_purchase_grn_items as gi')->select('gi.id', 'gi.part_no', 'gi.part_number', 'gi.hscode', 'gi.qty', 'gi.unitprice', 'gi.value', 'gi.discount', 'gi.tax', 'gi.taxableamount', 'gi.vatamount', 'gi.po_id', 'gi.qty as po_qty', 'gi.qty as executed_qty', 'gi.qty as executed_qty', 'gi.qty as grn_qty', 'itm.product_type', 'itm.description', 'gi.fright', 'gi.customcharges', 'gi.sort_id')
    //                 ->join('sm_items as itm', 'itm.id', 'gi.part_no')
    //                 ->where('grn_id', $id)->orderby('sort_id', 'asc')->get();
    //         } else {
    //             // $grn_items = DB::table('sys_purchase_grn_items as gi')->select('gi.id','gi.part_no','gi.part_number','gi.qty','gi.unitprice','gi.value','gi.discount','gi.tax','gi.taxableamount','gi.vatamount','pi.qty as po_qty','pi.issue_qty as executed_qty','pi.issue_qty as executed_qty','pi.grn_qty')
    //             // ->join('sys_purchase_order_items as pi','pi.part_number','gi.part_no')->where('grn_id',$grn->id)->get();

    //             $grn_items = DB::table('sys_purchase_grn_items as gi')->select(db::raw('gi.id,gi.part_no,gi.part_number,gi.hscode,gi.qty,gi.unitprice,gi.value,gi.discount,gi.tax,gi.taxableamount,gi.vatamount,gi.po_id,gi.sort_id,
    //             (SELECT IFNULL(max(description),0) FROM sm_items where id = gi.part_no) as description,
    //             (SELECT IFNULL(sum(pi.qty),0) FROM sys_purchase_order_items as pi where pi.part_number = gi.part_no) as po_qty,
    //             (SELECT IFNULL(sum(pi.issue_qty),0) FROM sys_purchase_order_items as pi where pi.part_number = gi.part_no) as executed_qty,
    //             (SELECT IFNULL(sum(pi.grn_qty),0) FROM sys_purchase_order_items as pi where pi.part_number = gi.part_no) as grn_qty
    //             '), 'itm.product_type', 'gi.fright', 'gi.customcharges')->join('sm_items as itm', 'itm.id', 'gi.part_no')->where('grn_id', $id)->orderby('sort_id', 'asc')->get();
    //         }

    //         $edit_list_srl = SysPurchaseGRNItemsSrlno::where('grn_id', $grn->id)->get();

    //         return view('backEnd.grn.manage_grn_invoice_edit', compact('grn_select_id', 'purchasegrn', 'currency', 'currencylist2', 'vendors', 'items', 'departments', 'paymentterms', 'company', 'shipping', 'suppliertype', 'purchasetype', 'countries', 'staff', 'grn', 'grn_items', 'edit_list_srl', 'salesman'));
    //     } catch (\Exception $e) {
    //         return $e;
    //         Toastr::error('Operation Failed', 'Failed');
    //         return redirect()->back();
    //     }
    // }

    public function edit($id)
    {
        try {
            $grn_select_id = $id;
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            if ($company_id == 1) {
                $query = SysPurchaseGRN::select(DB::raw('sys_purchase_grn.*, (SELECT GROUP_CONCAT(doc_file) FROM sys_purchase_order_att WHERE doc_id = sys_purchase_grn.po_id) AS attach, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_invoice WHERE ref_grn_id=sys_purchase_grn.id) AS piv_no, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_return WHERE ref_grn_id=sys_purchase_grn.id) AS prt_no, (SELECT SUM(taxableamount)+SUM(vatamount) AS amount FROM sys_purchase_grn_items WHERE grn_id=sys_purchase_grn.id) AS amount, sys_purchase_grn.deal_id AS code'));
                $query->orderby('id', 'desc');
                $purchasegrn = $query->get();
            } else {
                $query = SysPurchaseGRN::select(DB::raw('sys_purchase_grn.*, (SELECT GROUP_CONCAT(doc_file) FROM sys_purchase_order_att WHERE doc_id = sys_purchase_grn.po_id) AS attach, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_invoice WHERE ref_grn_id=sys_purchase_grn.id) AS piv_no, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_return WHERE ref_grn_id=sys_purchase_grn.id) AS prt_no, (SELECT SUM(taxableamount)+SUM(vatamount) AS amount FROM sys_purchase_grn_items WHERE grn_id=sys_purchase_grn.id) AS amount, sys_purchase_grn.deal_id AS code'));
                $query->where('company_id', $company_id);
                $query->orderby('id', 'desc');
                $purchasegrn = $query->get();
            }

            $currency = SysCurrencySettings::select('id', 'code', 'ex_rate')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $salesman = SysHelper::get_sales_persons();
            $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();

            $items = SysHelper::get_product_list($company_id);

            $grn = SysPurchaseGRN::where('id', $id)->first();


            $vendors = SysChartofAccounts::select('id', 'account_name', 'account_code')->where('id', $grn->vendors)->get();

            $currencylist2 = DB::table('sys_currency_rate as r')->select('r.id', 'r.from_currency', 'r.to_currency', 'c.code', 'r.rate')
                ->join('sys_currency as c', 'c.id', 'r.to_currency')
                ->where('r.status', 1)->where('r.from_currency', $grn->currency)
                ->orderBy('c.code', 'ASC')->get();

            $staff = SysHelper::get_staff_list();

            $departments = SmInspectingDepartment::all();
            $shipping = SysShipping::all();
            $suppliertype = SysSupplierType::orderby('title', 'asc')->get();
            $purchasetype = SysPurchaseType::orderby('title', 'asc')->get();
            $countries = SysCountries::orderby('name', 'asc')->get();
            $states = SysStates::orderby('name', 'asc')->get();

            $customer = SysHelper::get_customer_supplier_list($company_id);
            $customer_reference_list = SysHelper::get_customer_list_deal_lead_all_role();


            if ($grn->po_id == 0) {
                $grn_items = DB::table('sys_purchase_grn_items as gi')->select('gi.id', 'gi.part_no', 'gi.part_number', 'gi.hscode', 'gi.qty', 'gi.unitprice', 'gi.value', 'gi.discount', 'gi.tax', 'gi.taxableamount', 'gi.vatamount', 'gi.po_id', 'gi.qty as po_qty', 'gi.qty as executed_qty', 'gi.qty as executed_qty', 'gi.qty as grn_qty', 'itm.product_type', 'itm.description', 'gi.fright', 'gi.customcharges', 'gi.sort_id')
                    ->join('sm_items as itm', 'itm.id', 'gi.part_no')
                    ->where('grn_id', $id)->orderby('sort_id', 'asc')->get();
            } else {
                // $grn_items = DB::table('sys_purchase_grn_items as gi')->select('gi.id','gi.part_no','gi.part_number','gi.qty','gi.unitprice','gi.value','gi.discount','gi.tax','gi.taxableamount','gi.vatamount','pi.qty as po_qty','pi.issue_qty as executed_qty','pi.issue_qty as executed_qty','pi.grn_qty')
                // ->join('sys_purchase_order_items as pi','pi.part_number','gi.part_no')->where('grn_id',$grn->id)->get();

                $grn_items = DB::table('sys_purchase_grn_items as gi')->select(db::raw('gi.id,gi.part_no,gi.part_number,gi.hscode,gi.qty,gi.unitprice,gi.value,gi.discount,gi.tax,gi.taxableamount,gi.vatamount,gi.po_id,gi.sort_id,
                (SELECT IFNULL(max(description),0) FROM sm_items where id = gi.part_no) as description,
                (SELECT IFNULL(sum(pi.qty),0) FROM sys_purchase_order_items as pi where pi.part_number = gi.part_no) as po_qty,
                (SELECT IFNULL(sum(pi.issue_qty),0) FROM sys_purchase_order_items as pi where pi.part_number = gi.part_no) as executed_qty,
                (SELECT IFNULL(sum(pi.grn_qty),0) FROM sys_purchase_order_items as pi where pi.part_number = gi.part_no) as grn_qty
                '), 'itm.product_type', 'gi.fright', 'gi.customcharges')->join('sm_items as itm', 'itm.id', 'gi.part_no')->where('grn_id', $id)->orderby('sort_id', 'asc')->get();
            }

            $edit_list_srl = SysPurchaseGRNItemsSrlno::where('grn_id', $grn->id)->get();

            return compact('grn_select_id', 'purchasegrn', 'currency', 'currencylist2', 'vendors', 'items', 'departments', 'paymentterms', 'company', 'shipping', 'suppliertype', 'purchasetype', 'countries', 'staff', 'grn', 'grn_items', 'edit_list_srl', 'salesman', 'customer', 'states', 'customer_reference_list');
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function getDetails($id)
    {
        $data = $this->get_grn_pdf_data($id);
        if (count($data) > 0) {
            return view('backEnd.grn.grn_details', $data);
        } else {
            return "error!!";
        }
    }

    public function get_grn_pdf_data($id)
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
            $mob = "";
            $ship_company_name = "";
            $delivery_city = "";
            $delivery_zip_code = "";
            $delivery_country = "";
            $delivery_state = "";
            $m_trnno = "";
            $bill_contact_name = "";
            $ship_trnno = "";






            $grn = SysPurchaseGRN::find($id);

            $bill_contact_name = $grn->createdby->full_name;
            $ship_mob = SmStaff::select('mobile')->where('user_id', $grn->created_by)->first();
            $ship_mob = $ship_mob->mobile;

            if (!empty($grn)) {
                $company = SysCompany::find($grn->company_id);
                $grn_item = SysPurchaseGRNItems::where('grn_id', '=', $grn->id)->orderBy('sort_id')->get();
                $grn_item_srl = SysPurchaseGRNItemsSrlno::where('grn_id', '=', $grn->id)->get();


                $sup_email = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_chartofaccounts.id', $grn->vendors)->first();
                if (!empty($sup_email)) {
                    $add = SysCustSupplAddressbook::where('cust_suppl_id', $sup_email->id)->first();
                }


                $contact_name = $sup_email->customer_salutation . ' ' . $sup_email->first_name . ' ' . $sup_email->last_name;
                $email = $sup_email->email;
                $tel = $sup_email->contcat_number;
                $mob = $sup_email->mobile;

                $m_trnno = $sup_email->vat_number;

                $sub_data_list = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_chartofaccounts.id', $grn->shipping_supplier)->first();

                if (isset($sub_data_list)) {


                    $ship_trnno = $sub_data_list->vat_number;


                }


                if (!empty($add)) {
                    $address = $add->address;
                    $address2 = $add->address2;
                    $city = $add->city;
                    $state = $add->statename->name;
                    $country = $add->countryname->name;
                }

                $ship_company_name = "";
                $ship_contact_name = $contact_name;
                $ship_email = $email;
                $ship_tel = $tel;
                $ship_address1 = $add->city;
                $ship_address2 = "";
                $delivery_city = $add->city;
                $delivery_zip_code = "";
                $delivery_country = $add->countryname->name;
                $delivery_state = $add->statename->name;

                $data = [
                    'grn' => $grn,
                    'company' => $company,
                    'grn_item' => $grn_item,
                    'grn_item_srl' => $grn_item_srl,
                    'mob' => $mob,
                    'm_trnno' => $m_trnno,

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
                    'bill_contact_name' => $bill_contact_name,
                    'ship_mob' => $ship_mob,
                    'ship_trnno' => $ship_trnno,
                ];

                return $data;
            }
            return [];
        } catch (\Throwable $th) {
            return [];
        }
    }



    public function view(Request $request, $id = null)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            if ($company_id == 1) {
                $query = SysPurchaseGRN::select(DB::raw('sys_purchase_grn.*, (SELECT GROUP_CONCAT(doc_file) FROM sys_purchase_order_att WHERE doc_id = sys_purchase_grn.po_id) AS attach, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_invoice WHERE ref_grn_id=sys_purchase_grn.id) AS piv_no, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_return WHERE ref_grn_id=sys_purchase_grn.id) AS prt_no, (SELECT SUM(taxableamount)+SUM(vatamount) AS amount FROM sys_purchase_grn_items WHERE grn_id=sys_purchase_grn.id) AS amount, sys_purchase_grn.deal_id AS code'));
            } else {
                $query = SysPurchaseGRN::select(DB::raw('sys_purchase_grn.*, (SELECT GROUP_CONCAT(doc_file) FROM sys_purchase_order_att WHERE doc_id = sys_purchase_grn.po_id) AS attach, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_invoice WHERE ref_grn_id=sys_purchase_grn.id) AS piv_no, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_return WHERE ref_grn_id=sys_purchase_grn.id) AS prt_no, (SELECT SUM(taxableamount)+SUM(vatamount) AS amount FROM sys_purchase_grn_items WHERE grn_id=sys_purchase_grn.id) AS amount, sys_purchase_grn.deal_id AS code'));
            }

            $ctrl_doc_number = "";
            $ctrl_supplier = "";
            $ctrl_customer = "";
            $ctrl_deal_number = "";
            $ctrl_purchase_invoice_number = "";
            $ctrl_purchase_order_number = "";
            $ctrl_purchase_return_number = "";
            $ctrl_date = "";

            if (count($request->all()) > 0) {



                if ($request->documents_number != "") {
                    $query->where('doc_number', 'like', '%' . $request->documents_number . '%');
                    $ctrl_doc_number = $request->documents_number;
                }
                if ($request->supplier != "") {
                    $query->where('vendors', $request->supplier);
                    $ctrl_supplier = $request->supplier;
                }
                if ($request->customer != "") {
                    $query->where('narration', 'like', '%' . $request->customer . '%');
                    $ctrl_customer = $request->customer;
                }
                if ($request->deal_number != "") {
                    $query->where('deal_id', 'like', '%' . SysHelper::get_dealid_from_code($request->deal_number) . '%');
                    $ctrl_deal_number = $request->deal_number;
                }
                if ($request->purchase_invoice_number != "") {
                    $inv_nos = SysPurchaseGRN::join('sys_purchase_invoice', 'sys_purchase_invoice.ref_grn_id', 'sys_purchase_grn.id')
                        ->where('sys_purchase_invoice.doc_number', 'like', '%' . $request->purchase_invoice_number . '%')->pluck('sys_purchase_grn.doc_number');
                    $query->wherein('doc_number', $inv_nos);
                    $ctrl_purchase_invoice_number = $request->purchase_invoice_number;
                }
                if ($request->purchase_order_number != "") {
                    $po_nos = SysPurchaseGRN::join('sys_purchase_order', 'sys_purchase_order.id', 'sys_purchase_grn.po_id')
                        ->where('sys_purchase_order.doc_number', 'like', '%' . $request->purchase_order_number . '%')->pluck('sys_purchase_grn.doc_number');
                    $query->wherein('doc_number', $po_nos);
                    $ctrl_purchase_order_number = $request->purchase_order_number;
                }
                if ($request->purchase_return_number != "") {
                    $prt_nos = SysPurchaseGRN::join('sys_purchase_return', 'sys_purchase_return.ref_grn_id', 'sys_purchase_grn.id')
                        ->where('sys_purchase_return.doc_number', 'like', '%' . $request->purchase_return_number . '%')->pluck('sys_purchase_grn.doc_number');
                    $query->wherein('doc_number', $prt_nos);
                    $ctrl_purchase_return_number = $request->purchase_return_number;
                }

                if ($request->date != "") {
                    $query->where('grn_date', SysHelper::normalizeToYmd($request->date));
                    $ctrl_date = $request->date;
                }
            }


            if ($company_id == 1) {
                $query->orderby('id', 'desc');
                $purchasegrn = $query->get();
            } else {

                $query->where('company_id', $company_id);
                $query->orderby('id', 'desc');
                $purchasegrn = $query->get();
            }



            if (count($purchasegrn) == 0) {
                //Toastr::error('No data found', 'Failed');
                //return redirect()->back();
            }





            $active_id = $id;
            $data = [];
            $action = false;
            $editData = [];
            $addData = [];


            if ($request->has('grn_action')) {
                $poAction = $request->input('grn_action');

                if ($poAction === 'add') {
                    $action = 'add';
                    $addData = $this->create(); // Get all data for adding
                } elseif ($poAction === 'edit') {
                    $action = 'edit';
                    $editData = $this->edit($active_id); // Get all data for editing
                }
            } else {


                if ($id != null) {
                    $data = $this->get_grn_pdf_data($id);
                } else {

                    $firstRecord = $purchasegrn->first();
                    if ($firstRecord) {
                        $active_id = $firstRecord->id;
                        $data = $this->get_grn_pdf_data($active_id);
                    }
                }
            }





            // $id = $purchasegrn->first()->id;

            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();
            $salesman = SysHelper::get_sales_persons();

            $vendors = SysHelper::get_supplier_list_all($company_id);
            $items = SysHelper::get_product_list($company_id);

            $grn = SysPurchaseGRN::where('id', $active_id)->first();
            $staff = SysHelper::get_staff_list();

            $departments = SmInspectingDepartment::all();
            $shipping = SysShipping::all();
            $suppliertype = SysSupplierType::orderby('title', 'asc')->get();
            $purchasetype = SysPurchaseType::orderby('title', 'asc')->get();
            $countries = SysCountries::orderby('name', 'asc')->get();
            $states = SysStates::orderby('name', 'asc')->get();



            if (isset($grn)) {
                if ($grn->po_id == 0) {
                    $grn_items = DB::table('sys_purchase_grn_items as gi')->select('gi.id', 'gi.part_no', 'gi.part_number', 'gi.qty', 'gi.unitprice', 'gi.value', 'gi.discount', 'gi.qty as po_qty', 'gi.qty as executed_qty', 'gi.qty as executed_qty', 'gi.fright', 'gi.customcharges')->where('grn_id', $grn->id)->get();
                } else {
                    $grn_items = DB::table('sys_purchase_grn_items as gi')->select('gi.id', 'gi.part_no', 'gi.part_number', 'gi.qty', 'gi.unitprice', 'gi.value', 'gi.discount', 'pi.qty as po_qty', 'pi.issue_qty as executed_qty', 'pi.issue_qty as executed_qty', 'gi.fright', 'gi.customcharges')
                        ->leftjoin('sys_purchase_order_items as pi', 'pi.id', 'gi.po_id')->where('grn_id', $grn->id)->get();
                }
            } else {
                $grn_items = [];
            }





            return view('backEnd.grn.manage_grn_invoice_view', compact('currency', 'vendors', 'items', 'departments', 'paymentterms', 'company', 'shipping', 'suppliertype', 'purchasetype', 'countries', 'staff', 'grn', 'grn_items', 'salesman', 'purchasegrn', 'data', 'ctrl_doc_number', 'ctrl_supplier', 'ctrl_customer', 'ctrl_deal_number', 'ctrl_purchase_invoice_number', 'ctrl_purchase_order_number', 'ctrl_purchase_return_number', 'ctrl_date', 'active_id', 'action', 'editData', 'addData'));
        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update(Request $request)
    {

        DB::beginTransaction();
        try {
            $grn = SysPurchaseGRN::find($request->id);
            $grn->grn_date = Carbon::createFromFormat('d/m/Y', $request->grn_date)->format('Y-m-d');
            if ($request->doc_number != $request->doc_number_main) {
                $exists = SysPurchaseGRN::where('doc_number', $request->doc_number)->exists();
                if ($exists) {
                    DB::rollback();
                    Toastr::error('Operation Failed. Document number already exists', 'Failed');
                    return redirect()->back();
                }
                $grn->doc_number = $request->doc_number;
            }
            //$grn->po_id = $request->po_id;            
            $grn->vendors = $request->vendors;
            $grn->currency = $request->currency;
            $grn->lpo_number = $request->lpo_number;
            $grn->lpo_date = Carbon::createFromFormat('d/m/Y', $request->lpo_date)->format('Y-m-d');
            $grn->payment_terms = $request->payment_terms;
            $grn->bill_number = $request->bill_number;
            $grn->bill_date = Carbon::createFromFormat('d/m/Y', $request->bill_date)->format('Y-m-d');
            $grn->awbno = $request->awbno;
            $grn->boeno = $request->boeno;
            $grn->warehouse = $request->warehouse;
            $grn->reference = $request->reference;
            $grn->narration = $request->narration;
            $grn->deal_id = SysHelper::get_dealid_from_code_list($request->deal_id);
            ;
            // $grn->sales_person = $request->sales_person;


            $sales_person = $request->sales_person;
            if (!is_null($sales_person) && $sales_person !== '') {
                if (is_numeric($sales_person)) {
                    // Selected from dropdown (user ID)
                    $grn->sales_person = (int) $sales_person;
                    $grn->sales_person_name = null;
                } else {
                    // Manually entered name
                    $grn->sales_person = null;
                    $grn->sales_person_name = trim($sales_person);
                }
            }

            $grn->status = 1;
            $grn->updated_by = Auth::user()->id;
            $grn->updated_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $grn->company_id = session('logged_session_data.company_id');


            $grn->shipping_supplier = $request->shipping_supplier;
            $grn->shipping_name = $request->shipping_name;
            $grn->shipping_email = $request->shipping_email;
            $grn->shipping_contact_no = $request->shipping_contact_no;
            $grn->shipping_address_1 = $request->shipping_address_1;
            $grn->supplier_country = $request->supplier_country;
            $grn->supplier_state = $request->supplier_state;
            $grn->vat_percent = $request->vat_percent;
            $grn->vat_number = $request->vat_number;
            $grn->supplier_type = $request->supplier_type;
            $grn->purchase_type = $request->purchase_type;
            $grn->ref_company_id = isset($request->ref_company_id)
                ? implode(',', $request->ref_company_id)
                : null;

            $grn->save();

            SysPurchaseGRNItems::where('grn_id', $grn->id)->delete();
            SysItemStock::where('grn_id', $grn->id)->delete();
            SysPurchaseGRNItemsSrlno::where('grn_id', $grn->id)->delete();
            for ($i = 0; $i < count($request->part_number); $i++) {
                //if($request->qty[$i] !="" && $request->qty[$i] <= $request->grn_qty[$i] && $request->qty[$i] > 0 ){
                if ($request->qty[$i] != "" && $request->qty[$i] > 0) {
                    $grnitms = new SysPurchaseGRNItems();
                    $grnitms->grn_id = $grn->id;
                    //$grnitms->po_id = $grn->po_id;
                    if (isset($request->item_po_id)) {
                        if (count($request->item_po_id) > 0 && $request->item_po_id[0] != "") {
                            //$grnitms->po_id = $request->item_po_id[$i] ?? 0;
                        }
                    }
                    if (isset($request->list_po_id)) {
                        if (count($request->list_po_id) > 0 && $request->list_po_id[0] != "") {
                            //$grnitms->po_id = $request->list_po_id[$i] ?? 0;
                        }
                    }
                    $grnitms->po_id = $grn->po_id ?? 0;
                    $grnitms->part_no = $request->part_number[$i];

                    $grnitms->description = $request->description[$i];
                    $grnitms->part_number = $request->part_number_txt[$i];
                    $grnitms->tax = $request->tax[$i];
                    $grnitms->qty = $request->qty[$i];
                    $grnitms->unitprice = (float) str_replace(',', '', $request->unitprice[$i] ?? 0);

                    $value = (float) str_replace(',', '', $request->value[$i] ?? 0);
                    $discount = (float) str_replace(',', '', $request->discount[$i] ?? 0);
                    $customcharges = (float) str_replace(',', '', $request->customcharges[$i] ?? 0);
                    $fright = (float) str_replace(',', '', $request->fright[$i] ?? 0);

                    $grnitms->value = $value;
                    $grnitms->discount = $discount;
                    $grnitms->customcharges = $customcharges;
                    $grnitms->fright = $fright;

                    $grnitms->taxableamount = $value - $discount + ($customcharges + $fright);
                    $grnitms->vatamount = ($value - $discount + ($customcharges + $fright)) * $request->tax[$i] / 100;
                    $grnitms->status = 1;
                    $grnitms->sort_id = $request->sort_id[$i];
                    $grnitms->save();

                    $str_arr = explode(",", $request->serial_no[$i]);
                    /*$str_arr = collect(preg_split('/[\s,]+/', $request->srl[$i], -1, PREG_SPLIT_NO_EMPTY))
                    ->map(fn($s) => strtoupper(trim($s)))->unique()->values()->toArray();*/
                    foreach ($str_arr as $srl) {
                        $values = array('grn_id' => $grn->id, 'part_no' => $request->part_number[$i], 'srl_no' => $srl, 'item_id' => $grnitms->id);
                        DB::table('sys_purchase_grn_items_srlno')->insert($values);
                    }

                    if (isset($request->item_po_id)) {
                        if (count($request->item_po_id) > 0 && $request->item_po_id[0] != "") {
                            $grn_quantity = DB::table('sys_purchase_order_items')->where('po_id', $request->item_po_id[$i])->where('part_number', $request->part_number[$i])->sum('grn_qty');
                            DB::table('sys_purchase_order_items')->where('po_id', $grn->po_id)->where('part_number', $request->part_number[$i])
                                ->update(['grn_qty' => $grn_quantity + $request->qty[$i]]);
                        }
                    }
                    if (isset($request->list_po_id)) {
                        if (count($request->list_po_id) > 0 && $request->list_po_id[0] != "") {
                            $grn_quantity = DB::table('sys_purchase_order_items')->where('po_id', $request->list_po_id[$i])->where('part_number', $request->part_number[$i])->sum('grn_qty');
                            DB::table('sys_purchase_order_items')->where('po_id', $grn->po_id)->where('part_number', $request->part_number[$i])
                                ->update(['grn_qty' => $grn_quantity + $request->qty[$i]]);
                        }
                    }

                    $discount = ($request->discount[$i] === '' ? '0.00' : $request->discount[$i]);
                    $istock = new SysItemStock();
                    $istock->grn_id = $grn->id;
                    $istock->account_id = $request->vendors;
                    $istock->partno = $request->part_number[$i];
                    $istock->qty_in = $request->qty[$i];
                    $istock->price_in = ($value - $discount) / $request->qty[$i];
                    $istock->refno = $grn->lpo_number;
                    $istock->doc_number = $grn->doc_number;
                    $istock->doc_date = $grn->grn_date;
                    $istock->deal_id = $grn->deal_id;
                    $istock->slno = $request->serial_no[$i];
                    $istock->status = 1;
                    $istock->created_by = Auth::user()->id;
                    $istock->company_id = session('logged_session_data.company_id');
                    $istock->currency_id = $request->currency;

                    $sales_person = $request->sales_person;
                    if (!is_null($sales_person) && $sales_person !== '') {
                        if (is_numeric($sales_person)) {
                            // Selected from dropdown (user ID)
                            $istock->sales_person = (int) $sales_person;
                        } else {
                            // Manually entered name
                            $istock->sales_person = null;
                        }
                    }

                    // $istock->sales_person = $request->sales_person;
                    $istock->item_id = $grnitms->id;
                    $istock->save();
                }
            }
            if (isset($request->item_po_id)) {
                if (count($request->item_po_id) > 0 && $request->item_po_id[0] != "") {
                    for ($a = 0; $a < count($request->item_po_id); $a++) {
                        $po = SysPurchaseOrderItems::where('po_id', $grn->po_id)->sum('qty');
                        $gr = SysPurchaseGRNItems::where('po_id', $grn->po_id)->where('po_id', '!=', 0)->sum('qty');
                        //$gr=SysPurchaseGRNItems::where('po_id',$request->po_id)->sum('qty');
                        if ($po <= $gr && $request->item_po_id[$a] != 0) {
                            DB::table('sys_purchase_order')->where('id', $grn->po_id)->update(['grn_status' => 1]);
                        }
                    }
                }
            }
            if (isset($request->list_po_id)) {
                if (count($request->list_po_id) > 0 && $request->list_po_id[0] != "") {
                    for ($a = 0; $a < count($request->list_po_id); $a++) {
                        $po = SysPurchaseOrderItems::where('po_id', $grn->po_id)->sum('qty');
                        $gr = SysPurchaseGRNItems::where('po_id', $grn->po_id)->where('po_id', '!=', 0)->sum('qty');
                        //$gr=SysPurchaseGRNItems::where('po_id',$request->po_id)->sum('qty');
                        if ($po <= $gr && $request->list_po_id[$a] != 0) {
                            DB::table('sys_purchase_order')->where('id', $grn->po_id)->update(['grn_status' => 1]);
                        }
                    }
                }
            }

            DB::commit();
            Toastr::success('Operation successful', 'Success');

            

            if ($request->has('cfc_name') && count($request->cfc_name) > 0) {
                $pi = null;
                $isPiLinked = false;

                if ($request->filled('cfc_pi_id')) {
                    $pi = SysPurchaseInvoice::where('id', $request->cfc_pi_id)->first();
                }
                if (is_null($pi)) {
                    $pi = SysPurchaseInvoice::whereRaw('FIND_IN_SET(?, ref_grn_id)', [$request->id])->first();
                }
                if (!is_null($pi)) {
                    $isPiLinked = true;
                }

                // If PI exists for this GRN, PI becomes the source of truth.
                if ($isPiLinked) {
                    DB::table('sys_purchase_grn_cf_charges')->where('grn_id', $request->id)->delete();
                    DB::table('sys_purchase_invoice_cf_charges')->where('pi_id', $pi->id)->delete();
                } else {
                    DB::table('sys_purchase_grn_cf_charges')->where('grn_id', $request->id)->delete();
                }

                for ($i = 0; $i < count($request->cfc_name); $i++) {
                    $hasAnyChargeValue = !empty($request->cfc_name[$i]) || !empty($request->cfc_amount[$i]) || !empty($request->cfc_bill_no[$i]) || !empty($request->cfc_remarks[$i]) || !empty($request->cfc_date[$i]) || !empty($request->cfc_credit_account[$i]);
                    if (!$hasAnyChargeValue) {
                        continue;
                    }

                    $creditAccountId = (!empty($request->cfc_credit_account[$i]) && $request->cfc_credit_account[$i] !== 'none')
                        ? $request->cfc_credit_account[$i]
                        : ($isPiLinked ? $pi->vendors : $request->vendors);
                    $chargeAmount = str_replace(',', '', $request->cfc_amount[$i] ?? '0');

                    if ($isPiLinked) {
                        $cfc = new SysPurchaseInvoiceCFCharges();
                        $cfc->pi_id = $pi->id;
                        $cfc->pi_doc_number = $pi->doc_number;
                        $cfc->date = $request->cfc_date[$i] ? SysHelper::normalizeToYmd($request->cfc_date[$i]) : null;
                        $cfc->bill_number = $request->cfc_bill_no[$i] ?? null;
                        $cfc->cfc_name = $request->cfc_name[$i] ?? null;
                        $cfc->cfc_credit_account = $creditAccountId;
                        $cfc->cfc_amount = $chargeAmount;
                        $cfc->cfc_remarks = $request->cfc_remarks[$i] ?? null;
                        $cfc->status = 1;
                        $cfc->created_by = Auth::user()->id;
                        $cfc->save();
                    } else {
                        $cfc = new SysPurchaseGrnCfCharges();
                        $cfc->grn_id = $request->id;
                        $cfc->date = $request->cfc_date[$i] ? SysHelper::normalizeToYmd($request->cfc_date[$i]) : null;
                        $cfc->bill_number = $request->cfc_bill_no[$i] ?? null;
                        $cfc->cfc_name = $request->cfc_name[$i] ?? null;
                        $cfc->cfc_credit_account = $creditAccountId;
                        $cfc->cfc_amount = $chargeAmount;
                        $cfc->cfc_remarks = $request->cfc_remarks[$i] ?? null;
                        $cfc->status = 1;
                        $cfc->created_by = Auth::user()->id;
                        $cfc->save();
                    }

                    if ($isPiLinked && !empty($request->cfc_name[$i]) && (float) $chargeAmount > 0) {
                        SysHelper::trn_chartof_accounts_transaction($creditAccountId, $pi->id, $pi->doc_number, $pi->pi_date, 'purchaseinvoice', '0.00', $chargeAmount, $request->cfc_remarks[$i] ?? '', 1, 0, "", $i + 2);
                        SysHelper::trn_chartof_accounts_transaction($request->cfc_name[$i], $pi->id, $pi->doc_number, $pi->pi_date, 'purchaseinvoice', $chargeAmount, '0.00', $request->cfc_remarks[$i] ?? '', 1, 0, "", $i + 2);
                    }
                }

                // Mirror PI charges back to GRN so GRN edit shows latest instantly.
                if ($isPiLinked) {
                    $piCharges = SysPurchaseInvoiceCFCharges::where('pi_id', $pi->id)->get();
                    DB::table('sys_purchase_grn_cf_charges')->where('grn_id', $request->id)->delete();
                    foreach ($piCharges as $piCharge) {
                        $grnCharge = new SysPurchaseGrnCfCharges();
                        $grnCharge->grn_id = $request->id;
                        $grnCharge->date = $piCharge->date;
                        $grnCharge->bill_number = $piCharge->bill_number;
                        $grnCharge->cfc_name = $piCharge->cfc_name;
                        $grnCharge->cfc_credit_account = $piCharge->cfc_credit_account;
                        $grnCharge->cfc_amount = $piCharge->cfc_amount;
                        $grnCharge->cfc_remarks = $piCharge->cfc_remarks;
                        $grnCharge->status = 1;
                        $grnCharge->created_by = Auth::user()->id;
                        $grnCharge->save();
                    }
                }
            }

            return redirect('goods-receipt-note-list/' . $grn->id);
        } catch (\Exception $e) {
            return $e;
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            DB::table('sys_item_stock')->where('grn_id', $id)->update(['status' => 2]);
            DB::table('sys_purchase_grn_items_srlno')->where('grn_id', $id)->update(['status' => 2]);
            DB::table('sys_purchase_grn_items')->where('grn_id', $id)->update(['status' => 2]);
            DB::table('sys_purchase_grn')->where('id', $id)->update(['status' => 2]);

            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function restore($id)
    {
        DB::beginTransaction();
        try {
            DB::table('sys_item_stock')->where('grn_id', $id)->update(['status' => 1]);
            DB::table('sys_purchase_grn_items_srlno')->where('grn_id', $id)->update(['status' => 1]);
            DB::table('sys_purchase_grn_items')->where('grn_id', $id)->update(['status' => 1]);
            DB::table('sys_purchase_grn')->where('id', $id)->update(['status' => 1]);

            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function addgrnitems(Request $request)
    {
        try {
            if ($request->vendors_old != $request->vendors) {
                SysPurchaseGRN::where('id', $request->grn_id)->update(['vendors' => $request->vendors]);
                SysPurchaseGRNItems::where('grn_id', $request->grn_id)->delete();
                SysPurchaseGRNItemsSrlno::where('id', $request->grn_id)->delete();
            }

            $item_id = DB::table('sys_purchase_grn_items')->insertGetId(
                [
                    'grn_id' => $request->grn_id,
                    'po_id' => $request->po_id,
                    'part_no' => $request->part_no,
                    'part_number' => $request->part_number,
                    'hscode' => $request->hscode,
                    'tax' => $request->tax,
                    'qty' => $request->qty,
                    'unitprice' => $request->unitprice,
                    'value' => $request->value,
                    'discount' => $request->discount,
                    'fright' => $request->fright,
                    'customcharges' => $request->customcharges,
                    'taxableamount' => $request->taxableamount,
                    'vatamount' => $request->vatamount,
                    'sort_id' => $request->sort_id,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );
            $str_arr = explode(",", $request->srl_no);
            foreach ($str_arr as $srl) {
                $values = array('grn_id' => $request->grn_id, 'part_no' => $request->part_no, 'srl_no' => $srl, 'item_id' => $item_id);
                DB::table('sys_purchase_grn_items_srlno')->insert($values);
            }
            $ret = 'success';
            return json_encode(array('data' => $ret));

            $ret = SysPurchaseGRNItems::select('sys_purchase_grn_items.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_purchase_grn_items.part_number')
                ->where('grn_id', $request->grn_id)->orderby('sort_id')->get();
            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = $e;
            //$ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }
    function updategrnitems(Request $request)
    {
        try {
            DB::table('sys_purchase_grn_items')->where('id', $request->id)->update(
                [
                    'part_no' => $request->part_no,
                    'part_number' => $request->part_number,
                    'tax' => $request->tax,
                    'qty' => $request->qty,
                    'unitprice' => $request->unitprice,
                    'value' => $request->value,
                    'discount' => $request->discount,
                    'fright' => $request->fright,
                    'customcharges' => $request->customcharges,
                    'taxableamount' => $request->taxableamount,
                    'vatamount' => $request->vatamount,
                    'sort_id' => $request->sort_id,
                    'status' => 1,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]
            );
            DB::table('sys_purchase_grn_items_srlno')->where(['grn_id' => $request->grn_id, 'part_no' => $request->part_no, 'item_id' => $request->id])->delete();

            $str_arr = explode(",", $request->srl_no);
            foreach ($str_arr as $srl) {
                $values = array('grn_id' => $request->grn_id, 'part_no' => $request->part_no, 'srl_no' => $srl, 'item_id' => $request->id);
                DB::table('sys_purchase_grn_items_srlno')->insert($values);
            }

            $ret = SysPurchaseGRNItems::select('sys_purchase_grn_items.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_purchase_grn_items.part_number')
                ->where('grn_id', $request->grn_id)->orderby('sort_id')->get();
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

    function deletegrnitems(Request $request)
    {
        try {
            DB::table('sys_purchase_grn_items')->where('id', $request->id)->delete();

            $ret = SysPurchaseGRNItems::select('sys_purchase_grn_items.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_purchase_grn_items.part_number')
                ->where('grn_id', $request->grn_id)->orderby('sort_id')->get();
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


    public function download($id)
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
            $delivery_city = "";
            $delivery_zip_code = "";
            $delivery_country = "";
            $delivery_state = "";
            $m_trnno = "";
            $bill_contact_name = "";
            $mob = "";
            $ship_trnno = "";



            $grn = SysPurchaseGRN::find($id);

            $bill_contact_name = $grn->createdby->full_name;
            $ship_mob = SmStaff::select('mobile')->where('user_id', $grn->created_by)->first();
            $ship_mob = $ship_mob->mobile;

            if (!empty($grn)) {
                $company = SysCompany::find($grn->company_id);
                $grn_item = SysPurchaseGRNItems::where('grn_id', '=', $grn->id)->get();
                $grn_item_srl = SysPurchaseGRNItemsSrlno::where('grn_id', '=', $grn->id)->get();

                $sup_email = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_chartofaccounts.id', $grn->vendors)->first();
                if (!empty($sup_email)) {
                    $add = SysCustSupplAddressbook::where('cust_suppl_id', $sup_email->id)->first();
                }


                $contact_name = $sup_email->customer_salutation . ' ' . $sup_email->first_name . ' ' . $sup_email->last_name;
                $email = $sup_email->email;
                $tel = $sup_email->contcat_number;
                $m_trnno = $sup_email->vat_number;
                $mob = $sup_email->mobile;

                $sub_data_list = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_chartofaccounts.id', $grn->shipping_supplier)->first();

                if (isset($sub_data_list)) {


                    $ship_trnno = $sub_data_list->vat_number;


                }



                if (!empty($add)) {
                    $address = $add->address;
                    $address2 = $add->address2;
                    $city = $add->city;
                    $state = $add->statename->name;
                    $country = $add->countryname->name;
                }

                // if($grn->deal_id != 0 && $grn->deal_id != "") {
                //     $deal_details = SysCrmDeals::where('id',$grn->deal_id)->first();

                //     if(isset($deal_details)){
                //         if($deal_details->delivery_company != "") { $ship_company_name = $deal_details->delivery_company; } else { $ship_company_name = $deal_details->customername->name; }

                //         if($deal_details->delivery_address1 != "") { $ship_address1 = $deal_details->delivery_address1; } else { $ship_address1 = $deal_details->address; }
                //         if($deal_details->delivery_address2 != "") { $ship_address2 = $deal_details->delivery_address2; } else { $ship_address2 = ""; }
                //         if($deal_details->delivery_city != "") { $delivery_city = $deal_details->delivery_city; } else { $delivery_city = $add->city; }
                //         if($deal_details->delivery_zip_code != "") { $delivery_zip_code = $deal_details->delivery_zip_code; } else { $delivery_zip_code = ""; }
                //         if($deal_details->delivery_country != "") { $delivery_country = $deal_details->country->name; } else { $delivery_country = $add->countryname->name; }
                //         if($deal_details->delivery_state != "") { $delivery_state = $deal_details->state->name; } else { $delivery_state = $add->statename->name; }


                //         if($deal_details->delivery_name != "") { $ship_contact_name = $deal_details->delivery_name; } else { $ship_contact_name = $deal_details->cust_name; }
                //         if($deal_details->delivery_number != "") { $ship_tel = $deal_details->delivery_number; } else { $ship_tel = $deal_details->cust_no; }
                //         if($deal_details->delivery_email != "") { $ship_email = $deal_details->delivery_email; } else { $ship_email = $deal_details->cust_email; }
                //     }
                // }
                // else{
                $ship_company_name = "";
                $ship_contact_name = $contact_name;
                $ship_email = $email;
                $ship_tel = $tel;
                $ship_address1 = $add->city;
                $ship_address2 = "";
                $delivery_city = $add->city;
                $delivery_zip_code = "";
                $delivery_country = $add->countryname->name;
                $delivery_state = $add->statename->name;
                //}
                $data = [
                    'grn' => $grn,
                    'company' => $company,
                    'grn_item' => $grn_item,
                    'grn_item_srl' => $grn_item_srl,

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

                    'bill_contact_name' => $bill_contact_name,
                    'ship_mob' => $ship_mob,
                    'm_trnno' => $m_trnno,
                    'mob' => $mob,
                    'ship_trnno' => $ship_trnno,

                    // 'email' => $email,
                    // 'tel' => $tel,
                    // 'address' => $address,
                    // 'address2' => $address2,
                    // 'city' => $city,
                    // 'state' => $state,
                    // 'country' => $country,
                ];
                // return view('backEnd.pdf_print.grn_pdf', $data);
                $pdf = PDF::loadView('backEnd.pdf_print.grn_pdf', $data);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download($grn->doc_number . '-' . $grn->accountname->account_name . ".pdf");
            } else {
                return "error!!";
                //return view('web.syscom_credit_application_form');
            }
        } catch (\Throwable $th) {
            return $th;
        }
    }


    function goodsreceiptnoteupdate_currency(Request $request)
    {
        try {
            if ($request->to_currency_id != $request->from_currency_id) {

                $to_currency = SysCurrencyRate::where('id', $request->to_currency_id)->value('to_currency');

                SysPurchaseGRN::where('id', $request->cur_grn_id)->update(['currency' => $to_currency]);
                $qt = SysPurchaseGRNItems::where('grn_id', $request->cur_grn_id)->get();
                foreach ($qt as $t) {
                    //$old_price = $t->unitprice / $old_currancy->ex_rate;
                    $new_price = $t->unitprice * $request->to_currency_rate;

                    //$old_discount = $t->discount / $old_currancy->ex_rate;
                    $new_discount = $t->discount * $request->to_currency_rate;

                    SysPurchaseGRNItems::where('id', $t->id)->update(
                        [
                            'unitprice' => $new_price,
                            'value' => $new_price * $t->qty,
                            'discount' => $new_discount,
                            'taxableamount' => ($new_price * $t->qty) - $new_discount + ($t->fright + $t->customcharges),
                            'vatamount' => (($new_price * $t->qty) - $new_discount + ($t->fright + $t->customcharges)) * $t->tax / 100,
                        ]
                    );

                    SysItemStock::where('doc_number', $request->cur_grn_doc_no)->where('partno', $t->part_no)->update(
                        ['price_in' => ($new_price * $t->qty) - $new_discount, 'currency_id' => $to_currency]
                    );
                }
            }

            Toastr::success('Currency Updated Successfully. Please Update Sales Return', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    // discount
    function goodsreceiptnoteupdate_discount(Request $request)
    {
        try {
            if ($request->discount_amount != "") {
                $qt = SysPurchaseGRNItems::where('grn_id', $request->discount_amount_grn_id)->get();
                $discount_amount = $request->discount_amount;
                $total = $qt->sum('value');
                foreach ($qt as $t) {
                    $new_discount = ($t->value / $total) * $discount_amount;
                    SysPurchaseGRNItems::where('id', $t->id)->update(
                        [
                            'discount' => $new_discount,
                            'taxableamount' => ($t->unitprice * $t->qty) - $new_discount + ($t->fright + $t->customcharges),
                            'vatamount' => (($t->unitprice * $t->qty) - $new_discount + ($t->fright + $t->customcharges)) * $t->tax / 100,
                        ]
                    );
                }
            }
            Toastr::success('Discount Updated Successfully.', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    function addgrnitemscart_discount(Request $request)
    {
        try {
            if ($request->discount_amount != "") {
                $qt = SysPurchaseGRNItems::where('cart_id', session('logged_session_data.cart_id'))->get();
                $discount_amount = $request->discount_amount;
                $total = $qt->sum('value');
                foreach ($qt as $t) {
                    $new_discount = ($t->value / $total) * $discount_amount;
                    SysPurchaseGRNItems::where('id', $t->id)->update(
                        [
                            'discount' => $new_discount,
                            'taxableamount' => ($t->unitprice * $t->qty) - $new_discount + ($t->fright + $t->customcharges),
                            'vatamount' => (($t->unitprice * $t->qty) - $new_discount + ($t->fright + $t->customcharges)) * $t->tax / 100,
                        ]
                    );
                }
            }
            Toastr::success('Discount Updated Successfully.', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    // discount

    // freight
    function goodsreceiptnoteupdate_freight(Request $request)
    {
        try {
            if ($request->freight_amount != "") {
                $qt = SysPurchaseGRNItems::where('grn_id', $request->freight_amount_grn_id)->get();
                $freight_amount = $request->freight_amount;
                $total = $qt->sum('value');
                foreach ($qt as $t) {
                    $new_freight = ($t->value / $total) * $freight_amount;
                    SysPurchaseGRNItems::where('id', $t->id)->update(
                        [
                            'fright' => $new_freight,
                            'taxableamount' => ($t->unitprice * $t->qty) - $t->discount + $new_freight + $t->customcharges,
                            'vatamount' => (($t->unitprice * $t->qty) - $t->discount + $new_freight + $t->customcharges) * $t->tax / 100,
                        ]
                    );
                }
            }
            Toastr::success('Freight Updated Successfully.', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    function addgrnitemscart_freight(Request $request)
    {
        try {
            if ($request->freight_amount != "") {
                $qt = SysPurchaseGRNItems::where('cart_id', session('logged_session_data.cart_id'))->get();
                $freight_amount = $request->freight_amount;
                $total = $qt->sum('value');
                foreach ($qt as $t) {
                    $new_freight = ($t->value / $total) * $freight_amount;
                    SysPurchaseGRNItems::where('id', $t->id)->update(
                        [
                            'fright' => $new_freight,
                            'taxableamount' => ($t->unitprice * $t->qty) - $t->discount + $new_freight + $t->customcharges,
                            'vatamount' => (($t->unitprice * $t->qty) - $t->discount + $new_freight + $t->customcharges) * $t->tax / 100,
                        ]
                    );
                }
            }
            Toastr::success('Freight Updated Successfully.', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    // freight

    // custom
    function goodsreceiptnoteupdate_custom(Request $request)
    {
        try {
            if ($request->custom_amount != "") {
                $qt = SysPurchaseGRNItems::where('grn_id', $request->custom_amount_grn_id)->get();
                $custom_amount = $request->custom_amount;
                $total = $qt->sum('value');
                foreach ($qt as $t) {
                    $new_custom = ($t->value / $total) * $custom_amount;
                    SysPurchaseGRNItems::where('id', $t->id)->update(
                        [
                            'customcharges' => $new_custom,
                            'taxableamount' => ($t->unitprice * $t->qty) - $t->discount + $new_custom + $t->fright,
                            'vatamount' => (($t->unitprice * $t->qty) - $t->discount + $new_custom + $t->fright) * $t->tax / 100,
                        ]
                    );
                }
            }
            Toastr::success('Custom Charges Updated Successfully.', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    function addgrnitemscart_custom(Request $request)
    {
        try {
            if ($request->custom_amount != "") {
                $qt = SysPurchaseGRNItems::where('cart_id', session('logged_session_data.cart_id'))->get();
                $custom_amount = $request->custom_amount;
                $total = $qt->sum('value');
                foreach ($qt as $t) {
                    $new_custom = ($t->value / $total) * $custom_amount;
                    SysPurchaseGRNItems::where('id', $t->id)->update(
                        [
                            'customcharges' => $new_custom,
                            'taxableamount' => ($t->unitprice * $t->qty) - $t->discount + $new_custom + $t->fright,
                            'vatamount' => (($t->unitprice * $t->qty) - $t->discount + $new_custom + $t->fright) * $t->tax / 100,
                        ]
                    );
                }
            }
            Toastr::success('Custom Charges Updated Successfully.', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    // custom

    public function search(Request $request)
    {
        $q = $request->get('query');
        $formattedDate = null;
        if (preg_match('/\d{2}[\/\-]\d{2}[\/\-]\d{4}/', $q)) {
            $normalized = str_replace('/', '-', $q);
            $formattedDate = date('Y-m-d', strtotime($normalized));
        }
        $r = SysHelper::get_data_by_role();

        $company_id = $r[0];
        if ($company_id == 1) {
            $query = SysPurchaseGRN::with('suppliername:id,name', 'currency_name:id,code', 'accountname:id,account_code,account_name')->select(DB::raw('sys_purchase_grn.*, (SELECT GROUP_CONCAT(doc_file) FROM sys_purchase_order_att WHERE doc_id = sys_purchase_grn.po_id) AS attach, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_invoice WHERE ref_grn_id=sys_purchase_grn.id) AS piv_no, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_return WHERE ref_grn_id=sys_purchase_grn.id) AS prt_no, (SELECT SUM(taxableamount)+SUM(vatamount) AS amount FROM sys_purchase_grn_items WHERE grn_id=sys_purchase_grn.id) AS amount, sys_purchase_grn.deal_id AS code'));
        } else {
            $query = SysPurchaseGRN::with('suppliername:id,name', 'currency_name:id,code', 'accountname:id,account_code,account_name')->select(DB::raw('sys_purchase_grn.*, (SELECT GROUP_CONCAT(doc_file) FROM sys_purchase_order_att WHERE doc_id = sys_purchase_grn.po_id) AS attach, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_invoice WHERE ref_grn_id=sys_purchase_grn.id) AS piv_no, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_return WHERE ref_grn_id=sys_purchase_grn.id) AS prt_no, (SELECT SUM(taxableamount)+SUM(vatamount) AS amount FROM sys_purchase_grn_items WHERE grn_id=sys_purchase_grn.id) AS amount, sys_purchase_grn.deal_id AS code'));
        }




        $amc_list = $query
            ->when($company_id != 1, function ($query) {
                $query->where('company_id', session('logged_session_data.company_id'));
            })
            ->where(function ($query) use ($q, $formattedDate) {
                $query->where(function ($qsub) use ($q) {
                    $dealId = SysHelper::get_dealid_from_code($q);

                    if ($q) {
                        $qsub->where('doc_number', 'like', "%{$q}%")
                            ->orWhereHas('suppliername', function ($q1) use ($q) {
                                $q1->where('name', 'like', "%{$q}%");
                            });

                        if (!empty($dealId) && $dealId != "0") {
                            $qsub->orWhere('deal_id', 'like', "%{$dealId}%");
                        }
                    }
                });

                if ($formattedDate) {
                    // Combine inside same group
                    $query->orWhere(function ($q2) use ($formattedDate) {
                        $q2->whereDate('grn_date', $formattedDate);
                    });
                }
            })
            ->orderBy('id', 'desc')
            ->get();




        // 🔹 Map additional formatted fields
        $amc_list = $amc_list->map(function ($item) {
            // Format amount using helper
            $item->formatted_amount = \App\SysHelper::com_curr_format($item->amount, 2, '.', ',');

            // Supplier name shortcut (avoid undefined relation)
            $item->cu_name = $item->suppliername->name ?? '';

            return $item;
        });

        return response()->json($amc_list);
    }
}

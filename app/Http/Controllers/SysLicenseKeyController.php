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
use App\SysDeliveryNote;
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
use App\SysPurchaseOrderItemsCart;
use App\SysPurchaseType;
use App\SysStates;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Nexmo\Numbers\Number;

use function GuzzleHttp\Promise\exception_for;

class SysLicenseKeyController extends Controller
{
    private static $licenseKeyColumnCache = [];

    public function __construct(){
        $this->middleware('PM');
    }

    private function licenseKeyHasColumn($column)
    {
        if (!array_key_exists($column, self::$licenseKeyColumnCache)) {
            self::$licenseKeyColumnCache[$column] = Schema::hasColumn('sys_purchase_grn_license_key', $column);
        }

        return self::$licenseKeyColumnCache[$column];
    }

    private function resolveLicenseItemId(Request $request)
    {
        $itemId = intval($request->item_id);
        if ($itemId > 0) {
            return $itemId;
        }

        $partNumber = trim((string) $request->part_number);
        if ($partNumber !== '') {
            $itemId = intval(SmItem::where('part_number', $partNumber)->value('id'));
        }

        return $itemId > 0 ? $itemId : 0;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
     public function report(Request $request,$id=null)
    {
        try{
            $r = SysHelper::get_data_by_role();
            $opb_date = Carbon::parse(date('Y-01-01'))->subDay()->format('Y-m-d');
            $company_id = $r[0];
            $from_date = date('Y-01-01');
            $to_date = date('Y-m-d');
            $part_number = "";
            $stocklist = [];
            $partnolist = [];
            $str_partno = $id;
            $license_key=[];
            $license=[];
            $dn_srl_list=[];
            $grn_srl_list=[];
            $pr_srl_list=[];
            $sr_srl_list=[];
            if($_POST){
                $from_date =Carbon::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');
                $to_date = Carbon::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');
             
                $str_partno = $request->part_number;
                $part_number = explode(',',$request->part_number);
                $opb_date = Carbon::parse($from_date)->subDay()->format('Y-m-d');
                
            if(count($part_number)>0){
                foreach($part_number as $part_no){
                        $partnolist[] = $part_no;
                        $stocklist[] = SysItemStock::select('sys_item_stock.doc_number','sys_item_stock.doc_date','sys_item_stock.refno','sys_item_stock.account_id','sys_item_stock.partno','sys_item_stock.description','sys_item_stock.qty_in','sys_item_stock.price_in','sys_item_stock.qty_out','sys_item_stock.price_out','sys_item_stock.deal_id','sys_item_stock.slno','sm_items.part_number','grn.reference as grn_reference','dln.supplier_name as dln_reference','srt.supplier_name as srt_reference','prt.reference as prt_reference')
                        ->join('sm_items','sm_items.id','sys_item_stock.partno')



                        ->leftjoin('sys_purchase_grn as grn', DB::raw("grn.doc_number COLLATE utf8mb4_unicode_ci"), DB::raw("sys_item_stock.doc_number COLLATE utf8mb4_unicode_ci"))
                        ->leftjoin('sys_delivery_note as dln', DB::raw("dln.doc_number COLLATE utf8mb4_unicode_ci"), DB::raw("sys_item_stock.doc_number COLLATE utf8mb4_unicode_ci"))
                        ->leftjoin('sys_sales_return as srt', DB::raw("srt.doc_number"), DB::raw("sys_item_stock.doc_number"))
                        ->leftjoin('sys_purchase_return as prt', DB::raw("prt.doc_number COLLATE utf8mb4_unicode_ci"), DB::raw("sys_item_stock.doc_number COLLATE utf8mb4_unicode_ci"))


                        ->whereRaw("DATE_FORMAT(sys_item_stock.doc_date, '%Y-%m-%d') >= '".$from_date."' and DATE_FORMAT(sys_item_stock.doc_date, '%Y-%m-%d') <= '".$to_date."'")
                        ->where('sm_items.part_number',$part_no)->where('sys_item_stock.status',1)->where('sm_items.status',1)
                        ->wherein('sys_item_stock.company_id',$company_id)
                        ->orderby('sys_item_stock.doc_date','asc')
                        ->get();
                    }
                    $item_ids = SmItem::wherein('part_number',$part_number)->value('id');
                    $license = DB::table('sys_purchase_grn_license_key as lk')->select('lk.*','grn.doc_number as grn_doc_number','ops.doc_number as ops_doc_number','dn.doc_number as dn_doc_number','sr.doc_number as sr_doc_number','pr.doc_number as pr_doc_number')
                    ->leftjoin('sys_purchase_grn as grn','grn.id','lk.grn_id')
                    ->leftjoin('sys_item_opening_stock as ops','ops.id','lk.opening_stock_id')
                    ->leftjoin('sys_delivery_note as dn','dn.id','lk.dn_id')
                    ->leftjoin('sys_sales_return as sr','sr.id','lk.sales_return_id')
                    ->leftjoin('sys_purchase_return as pr','pr.id','lk.purchase_return_id')
                    ->where('lk.item_id',$item_ids)->get();

                    $license_key = DB::table('sys_purchase_grn_license_key_trn as lk')->select('lk.*')->where('lk.item_id',$item_ids)->get();


                    $dn_srl_list = DB::table('sys_delivery_note_items_srl as srl')->select('srl.*','dn.doc_number')
                    ->join('sys_delivery_note as dn','dn.id','srl.dn_id')->where('srl.part_number',$item_ids)->get(); //dn_id, srl_no

                    $grn_srl_list = DB::table('sys_purchase_grn_items_srlno as srl')->select('srl.*','grn.doc_number')
                    ->join('sys_purchase_grn as grn','grn.id','srl.grn_id')->where('srl.part_no',$item_ids)->get(); //grn_id, srl_no
                    
                    $pr_srl_list = DB::table('sys_purchase_return_items_srlno as srl')->select('srl.*','pr.doc_number')
                    ->join('sys_purchase_return as pr','pr.id','srl.prt_id')->where('srl.part_no',$item_ids)->get(); //prt_id, srl_no

                    $sr_srl_list = DB::table('sys_sales_return_list_srl as srl')->select('srl.*','sr.doc_number')
                    ->join('sys_sales_return as sr','sr.id','srl.sr_id')->where('srl.part_number',$item_ids)->get(); //sr_id, srl_no

                    //$license_key = DB::table('sys_purchase_grn_license_key_trn as lk')->select('lk.*')->where('lk.item_id',$item_ids)->get();
                    //return $license_key;
                    
                    //return $license->where('ops_doc_number', 'OPD-1001')->where('status',1)->min('exp_date');

                }
            } else {
                if($id != ""){
                    $partnolist[] = $id;
                        $stocklist[] = SysItemStock::select('sys_item_stock.doc_number','sys_item_stock.doc_date','sys_item_stock.refno','sys_item_stock.account_id','sys_item_stock.partno','sys_item_stock.description','sys_item_stock.qty_in','sys_item_stock.price_in','sys_item_stock.qty_out','sys_item_stock.price_out','sys_item_stock.deal_id','sys_item_stock.slno','sm_items.part_number','grn.reference as grn_reference','dln.supplier_name as dln_reference','srt.supplier_name as srt_reference','prt.reference as prt_reference')
                        ->join('sm_items','sm_items.id','sys_item_stock.partno')
                        
                        ->leftjoin('sys_purchase_grn as grn', DB::raw("grn.doc_number COLLATE utf8mb4_unicode_ci"), DB::raw("sys_item_stock.doc_number COLLATE utf8mb4_unicode_ci"))
                        ->leftjoin('sys_delivery_note as dln', DB::raw("dln.doc_number COLLATE utf8mb4_unicode_ci"), DB::raw("sys_item_stock.doc_number COLLATE utf8mb4_unicode_ci"))
                        ->leftjoin('sys_sales_return as srt', DB::raw("srt.doc_number"), DB::raw("sys_item_stock.doc_number"))
                        ->leftjoin('sys_purchase_return as prt', DB::raw("prt.doc_number COLLATE utf8mb4_unicode_ci"), DB::raw("sys_item_stock.doc_number COLLATE utf8mb4_unicode_ci"))

                        ->whereRaw("DATE_FORMAT(sys_item_stock.doc_date, '%Y-%m-%d') >= '".$from_date."' and DATE_FORMAT(sys_item_stock.doc_date, '%Y-%m-%d') <= '".$to_date."'")
                        ->where('sm_items.part_number',$id)->where('sys_item_stock.status',1)->where('sm_items.status',1)
                        ->wherein('sys_item_stock.company_id',$company_id)
                        ->orderby('sys_item_stock.doc_date','asc')
                        ->get();
                        $part_number = [$id];
                        
                    $item_ids = SmItem::wherein('part_number',$part_number)->value('id');
                    $license = DB::table('sys_purchase_grn_license_key as lk')->select('lk.*','grn.doc_number as grn_doc_number','ops.doc_number as ops_doc_number','dn.doc_number as dn_doc_number','sr.doc_number as sr_doc_number','pr.doc_number as pr_doc_number')
                    ->leftjoin('sys_purchase_grn as grn','grn.id','lk.grn_id')
                    ->leftjoin('sys_item_opening_stock as ops','ops.id','lk.opening_stock_id')
                    ->leftjoin('sys_delivery_note as dn','dn.id','lk.dn_id')
                    ->leftjoin('sys_sales_return as sr','sr.id','lk.sales_return_id')
                    ->leftjoin('sys_purchase_return as pr','pr.id','lk.purchase_return_id')
                    ->where('lk.item_id',$item_ids)->get();
                    $license_key = DB::table('sys_purchase_grn_license_key_trn as lk')->select('lk.*')->where('lk.item_id',$item_ids)->get();

                    $dn_srl_list = DB::table('sys_delivery_note_items_srl as srl')->select('srl.*','dn.doc_number')
                    ->join('sys_delivery_note as dn','dn.id','srl.dn_id')->where('srl.part_number',$item_ids)->get(); //dn_id, srl_no

                    $grn_srl_list = DB::table('sys_purchase_grn_items_srlno as srl')->select('srl.*','grn.doc_number')
                    ->join('sys_purchase_grn as grn','grn.id','srl.grn_id')->where('srl.part_no',$item_ids)->get(); //grn_id, srl_no
                    
                    $pr_srl_list = DB::table('sys_purchase_return_items_srlno as srl')->select('srl.*','pr.doc_number')
                    ->join('sys_purchase_return as pr','pr.id','srl.prt_id')->where('srl.part_no',$item_ids)->get(); //prt_id, srl_no

                    $sr_srl_list = DB::table('sys_sales_return_list_srl as srl')->select('srl.*','sr.doc_number')
                    ->join('sys_sales_return as sr','sr.id','srl.sr_id')->where('srl.part_number',$item_ids)->get(); //sr_id, srl_no
                }
                
            }

            //return SysHelper::get_stock_ledger_opening_stock('R4W02A',$opb_date,$company_id);

            $items = SysHelper::get_product_list($company_id);

            return view('backEnd.inventory.LicenseKeyReport', compact('stocklist','partnolist','from_date','to_date','part_number','items','str_partno','opb_date','company_id','license_key','license','dn_srl_list','grn_srl_list','pr_srl_list','sr_srl_list'));
        }catch (\Exception $e) {
           return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }


    //  GRN CODE START
function add_grn_license_key_cart(Request $request)
    {
        try{
            $context = strtolower(trim((string) $request->context));
            if (!in_array($context, ['grn', 'sr'], true)) {
                $context = 'grn';
            }
            $companyId = session('logged_session_data.company_id');
            $cartId = session('logged_session_data.cart_id');

            $draftBase = SysPurchaseGrnLicenseKey::where('item_id', $request->item_id)
                ->where('grn_id', -1)
                ->where('type', 1)
                ->where('cart_id', $cartId)
                ->where('company_id', $companyId);

            if ($context === 'sr') {
                $draftBase->where('sales_return_id', -1);
            } else {
                $draftBase->where(function ($q) {
                    $q->whereNull('sales_return_id')->orWhere('sales_return_id', 0);
                })->where(function ($q) {
                    $q->whereNull('dn_id')->orWhere('dn_id', 0);
                })->where(function ($q) {
                    $q->whereNull('purchase_return_id')->orWhere('purchase_return_id', 0);
                })->when($this->licenseKeyHasColumn('stock_out_id'), function ($q) {
                    $q->whereRaw('COALESCE(stock_out_id,0)=0');
                });
            }

            $maxQty = intval($request->license_qty);
            $currentCount = (clone $draftBase)->count();

            $duplicates = [];

            $hasRowsPayload = $request->has('rows');
            $rowsInput = $request->rows;
            $rows = [];
            if (!empty($rowsInput)) {
                if (is_string($rowsInput)) {
                    $decoded = json_decode($rowsInput, true);
                    $rows = is_array($decoded) ? $decoded : [];
                } elseif (is_array($rowsInput)) {
                    $rows = $rowsInput;
                }
            }

            if ($hasRowsPayload) {
                $submittedKeys = [];
                foreach ($rows as $row) {
                    $key = trim((string)($row['license_key'] ?? ''));
                    if ($key === '') {
                        continue;
                    }
                    $submittedKeys[mb_strtolower($key)] = $key;
                }

                if (count($submittedKeys) > $maxQty) {
                    return json_encode(array('error' => 'Cannot save more than the allowed quantity of '.$maxQty.'.'));
                }

                // Full sync: remove keys deleted from draft before applying inserts/updates.
                if (count($submittedKeys) === 0) {
                    (clone $draftBase)->delete();
                    $currentCount = 0;
                } else {
                    (clone $draftBase)
                        ->whereNotIn('license_key', array_values($submittedKeys))
                        ->delete();
                    $currentCount = count($submittedKeys);
                }

                foreach ($rows as $row) {
                    $key = trim((string)($row['license_key'] ?? ''));
                    if ($key === '') {
                        continue;
                    }
                    $expDate = SysHelper::normalizeToYmd($row['exp_date'] ?? '');
                    $existing = (clone $draftBase)
                        ->where('item_id',$request->item_id)
                        ->where('license_key',$key)
                        ->first();
                    if ($existing) {
                        DB::table('sys_purchase_grn_license_key')
                            ->where('id', $existing->id)
                            ->update([
                                'exp_date' => $expDate,
                                'updated_by' => Auth::user()->id,
                                'updated_at' => Carbon::now('+04:00'),
                            ]);
                        continue;
                    }
                    if (!isset($seenInsertKeys)) {
                        $seenInsertKeys = [];
                    }
                    $normalizedKey = mb_strtolower($key);
                    if (isset($seenInsertKeys[$normalizedKey])) {
                        continue;
                    }
                    $seenInsertKeys[$normalizedKey] = true;

                    $globalDuplicate = SysPurchaseGrnLicenseKey::where('item_id', $request->item_id)
                        ->where('company_id', $companyId)
                        ->whereRaw('LOWER(license_key) = ?', [$normalizedKey])
                        ->exists();
                    if ($globalDuplicate) {
                        $duplicates[] = $key;
                        continue;
                    }

                    if ($existing == null) {
                        $rowData = [
                            'cart_id' => $cartId,
                            'grn_id' => '-1',
                            'item_id' => $request->item_id,
                            'license_key' => $key,
                            'license_qty' => 1,
                            'exp_date' => $expDate,
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                            'created_at' => Carbon::now('+04:00'),
                            'company_id' => $companyId,
                            'type' => 1,
                            'dn_id' => 0,
                            'purchase_return_id' => 0,
                            'sales_return_id' => ($context === 'sr' ? -1 : 0),
                        ];
                        if ($this->licenseKeyHasColumn('stock_out_id')) {
                            $rowData['stock_out_id'] = 0;
                        }
                        $data[] = $rowData;
                    }
                }
            } else {
                $licenseKeys = array_filter(array_unique(array_map('trim', explode(',', (string)$request->license_key))));
                foreach ($licenseKeys as $key) {
                    if ($currentCount >= $maxQty) {
                        break;
                    }
                    if ($key === '') {
                        continue;
                    }
                    $chk = (clone $draftBase)
                        ->where('item_id',$request->item_id)
                        ->where('license_key',$key)
                        ->count();
                    if ($chk == 0) {
                        $globalDuplicate = SysPurchaseGrnLicenseKey::where('item_id', $request->item_id)
                            ->where('company_id', $companyId)
                            ->whereRaw('LOWER(license_key) = ?', [mb_strtolower($key)])
                            ->exists();
                        if ($globalDuplicate) {
                            $duplicates[] = $key;
                            continue;
                        }
                        $rowData = [
                            'cart_id' => $cartId,
                            'grn_id' => '-1',
                            'item_id' => $request->item_id,
                            'license_key' => $key,
                            'license_qty' => 1,
                            'exp_date' => SysHelper::normalizeToYmd($request->exp_date),
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                            'created_at' => Carbon::now('+04:00'),
                            'company_id' => $companyId,
                            'type' => 1,
                            'dn_id' => 0,
                            'purchase_return_id' => 0,
                            'sales_return_id' => ($context === 'sr' ? -1 : 0),
                        ];
                        if ($this->licenseKeyHasColumn('stock_out_id')) {
                            $rowData['stock_out_id'] = 0;
                        }
                        $data[] = $rowData;
                        $currentCount++;
                    } else {
                        $duplicates[] = $key;
                    }
                }
            }
            if(isset($data) && !empty($data)){
                DB::table('sys_purchase_grn_license_key')->insert($data);
            }

            $ret = (clone $draftBase)->get();
            $response = ['data' => count($ret) > 0 ? $ret->toArray() : []];
            if (!empty($duplicates)) {
                $response['duplicate'] = true;
                $response['duplicate_keys'] = $duplicates;
                $response['message'] = 'Duplicate license key(s) already exist: ' . implode(', ', $duplicates);
            }
            return json_encode($response);
        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$e));
        }
    }
    function view_grn_license_key_cart(Request $request)
    {
        try{
            $context = strtolower(trim((string) $request->context));
            if (!in_array($context, ['grn', 'sr'], true)) {
                $context = 'grn';
            }
            $companyId = session('logged_session_data.company_id');
            $cartId = session('logged_session_data.cart_id');

            $ret = SysPurchaseGrnLicenseKey::where('item_id',$request->item_id)
                ->where('grn_id',-1)
                ->where('type',1)
                ->where('cart_id',$cartId)
                ->where('company_id',$companyId)
                ->when($context === 'sr', function ($q) {
                    $q->where('sales_return_id', -1);
                }, function ($q) {
                    $q->where(function ($qq) {
                        $qq->whereNull('sales_return_id')->orWhere('sales_return_id', 0);
                    })->where(function ($qq) {
                        $qq->whereNull('dn_id')->orWhere('dn_id', 0);
                    })->where(function ($qq) {
                        $qq->whereNull('purchase_return_id')->orWhere('purchase_return_id', 0);
                    })->when($this->licenseKeyHasColumn('stock_out_id'), function ($qq) {
                        $qq->whereRaw('COALESCE(stock_out_id,0)=0');
                    });
                })
                ->get();

            if ($context === 'sr') {
                $salesReturnId = max(0, intval($request->sales_return_id));
                if ($salesReturnId > 0) {
                    $savedKeys = SysPurchaseGrnLicenseKey::where('item_id', $request->item_id)
                        ->where('sales_return_id', $salesReturnId)
                        ->where('company_id', $companyId)
                        ->where('status', 1)
                        ->orderBy('exp_date', 'asc')
                        ->orderBy('id', 'asc')
                        ->get();

                    $ret = $ret->concat($savedKeys)->unique(function ($row) {
                        return mb_strtolower(trim((string) ($row->license_key ?? '')));
                    })->values();
                }
            }
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }
        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }
    function delete_grn_license_key_cart(Request $request)
    {
        try{
            DB::table('sys_purchase_grn_license_key')->where('id',$request->id)->delete();
            $ret = SysPurchaseGrnLicenseKey::where('item_id',$request->item_id)->where('grn_id',-1)->where('type',1)->where('cart_id',session('logged_session_data.cart_id'))->where('company_id',session('logged_session_data.company_id'))->get();
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }
        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }

    function update_grn_license_key_cart(Request $request)
    {
        try{
            $context = strtolower(trim((string) $request->context));
            if (!in_array($context, ['grn', 'sr'], true)) {
                $context = 'grn';
            }
            $newKey = trim($request->license_key);
            $expDate = SysHelper::normalizeToYmd($request->exp_date);
            if ($newKey === '') {
                return json_encode(array('error' => 'License key cannot be empty.'));
            }
            // Check duplicate globally for uniqueness (excluding this id)
            $dup = DB::table('sys_purchase_grn_license_key')
                ->where('item_id', $request->item_id)
                ->where('company_id', session('logged_session_data.company_id'))
                ->whereRaw('LOWER(license_key) = ?', [mb_strtolower($newKey)])
                ->where('id', '!=', $request->id)
                ->count();
            if ($dup > 0) {
                return json_encode(array('error' => 'Duplicate license key already exists.'));
            }
            DB::table('sys_purchase_grn_license_key')
                ->where('id', $request->id)
                ->update(['license_key' => $newKey, 'exp_date' => $expDate]);
            $ret = SysPurchaseGrnLicenseKey::where('item_id',$request->item_id)
                ->where('grn_id',-1)
                ->where('type',1)
                ->where('cart_id',session('logged_session_data.cart_id'))
                ->where('company_id',session('logged_session_data.company_id'))
                ->when($context === 'sr', function ($q) {
                    $q->where('sales_return_id', -1);
                }, function ($q) {
                    $q->where(function ($qq) {
                        $qq->whereNull('sales_return_id')->orWhere('sales_return_id', 0);
                    })->where(function ($qq) {
                        $qq->whereNull('dn_id')->orWhere('dn_id', 0);
                    })->where(function ($qq) {
                        $qq->whereNull('purchase_return_id')->orWhere('purchase_return_id', 0);
                    })->when($this->licenseKeyHasColumn('stock_out_id'), function ($qq) {
                        $qq->whereRaw('COALESCE(stock_out_id,0)=0');
                    });
                })
                ->get();
            return json_encode(array('data' => count($ret) > 0 ? $ret->toArray() : []));
        }catch (\Exception $e) {
            return json_encode(array('error' => $e->getMessage()));
        }
    }

    function add_grn_license_key_cart_excel(Request $request)
    {
        try{
            $context = strtolower(trim((string) $request->context));
            if (!in_array($context, ['grn', 'sr'], true)) {
                $context = 'grn';
            }
            $companyId = session('logged_session_data.company_id');
            $cartId = session('logged_session_data.cart_id');

            $draftBase = SysPurchaseGrnLicenseKey::where('item_id', $request->item_id)
                ->where('grn_id', -1)
                ->where('type', 1)
                ->where('cart_id', $cartId)
                ->where('company_id', $companyId);

            if ($context === 'sr') {
                $draftBase->where('sales_return_id', -1);
            } else {
                $draftBase->where(function ($q) {
                    $q->whereNull('sales_return_id')->orWhere('sales_return_id', 0);
                })->where(function ($q) {
                    $q->whereNull('dn_id')->orWhere('dn_id', 0);
                })->where(function ($q) {
                    $q->whereNull('purchase_return_id')->orWhere('purchase_return_id', 0);
                })->when($this->licenseKeyHasColumn('stock_out_id'), function ($q) {
                    $q->whereRaw('COALESCE(stock_out_id,0)=0');
                });
            }

            $selected_file = "";
            if ($request->hasFile('import_file') && $request->file('import_file')->isValid()) {
                // Store the file (e.g., in the 'uploads' folder)
                if ($request->file('import_file') != "") {
                    $file = $request->file('import_file');
                    $selected_file = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/product_upload/', $selected_file);
                    $selected_file = 'public/uploads/product_upload/' . $selected_file;
                }
            }

            $objPHPExcel = PHPExcel_IOFactory::load($selected_file);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow();

            $dataArray = $objPHPExcel->getActiveSheet()->toArray();

                $existingCount = (clone $draftBase)->count();
                $seenKeys = [];
                for($i=1; $i < count($dataArray); $i++){
                    $licenseKey = trim($dataArray[$i][0]);
                    if ($licenseKey === '') {
                        continue;
                    }
                    if ($existingCount >= intval($request->license_qty)) {
                        break;
                    }
                    $normalizedKey = mb_strtolower($licenseKey);
                    if (isset($seenKeys[$normalizedKey])) {
                        continue;
                    }
                    $seenKeys[$normalizedKey] = true;

                    $chk = (clone $draftBase)
                        ->where('item_id',$request->item_id)
                        ->where('license_key',$licenseKey)
                        ->count();
                    if ($chk > 0) {
                        continue;
                    }
                    $globalDuplicate = SysPurchaseGrnLicenseKey::where('item_id', $request->item_id)
                        ->where('company_id', $companyId)
                        ->whereRaw('LOWER(license_key) = ?', [$normalizedKey])
                        ->exists();
                    if ($globalDuplicate) {
                        continue;
                    }

                    $rowData = [
                        'cart_id' => $cartId,
                        'grn_id' => '-1',
                        'item_id' => $request->item_id,
                        'license_key' => $licenseKey,
                        'license_qty' => 1,
                        'exp_date' => SysHelper::normalizeToYmd($dataArray[$i][1] ?? ''),
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                        'company_id' => $companyId,
                        'type' => 1,
                        'dn_id' => 0,
                        'purchase_return_id' => 0,
                        'sales_return_id' => ($context === 'sr' ? -1 : 0),
                    ];
                    if ($this->licenseKeyHasColumn('stock_out_id')) {
                        $rowData['stock_out_id'] = 0;
                    }
                    $data[] = $rowData;
                    $existingCount++;
                }

            if(isset($data)){
                DB::table('sys_purchase_grn_license_key')->insert($data);
            }

            $ret = (clone $draftBase)->get();
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }
        }catch (\Exception $e) {
            //$ret = 'ERROR';
            $ret = $e;
            return json_encode(array('data'=>$ret));
        }
    }

    function add_grn_license_key(Request $request)
    {
        try{
            $maxQty = intval($request->license_qty);
            $companyId = session('logged_session_data.company_id');
            $currentCount = SysPurchaseGrnLicenseKey::where('item_id',$request->item_id)
                ->where('grn_id',$request->grn_id)
                ->where('type',1)
                ->where('company_id',$companyId)
                ->count();
            $duplicates = [];

            $hasRowsPayload = $request->has('rows');
            $rowsInput = $request->rows;
            $rows = [];
            if (!empty($rowsInput)) {
                if (is_string($rowsInput)) {
                    $decoded = json_decode($rowsInput, true);
                    $rows = is_array($decoded) ? $decoded : [];
                } elseif (is_array($rowsInput)) {
                    $rows = $rowsInput;
                }
            }

            if ($hasRowsPayload) {
                $submittedKeys = [];
                foreach ($rows as $row) {
                    $key = trim((string)($row['license_key'] ?? ''));
                    if ($key === '') continue;
                    $submittedKeys[mb_strtolower($key)] = $key;
                }

                if (count($submittedKeys) > $maxQty) {
                    return json_encode(array('error' => 'Cannot save more than the allowed quantity of '.$maxQty.'.'));
                }

                // Full sync: delete removed keys
                if (count($submittedKeys) === 0) {
                    DB::table('sys_purchase_grn_license_key')
                        ->where('item_id',$request->item_id)
                        ->where('grn_id',$request->grn_id)
                        ->where('type',1)
                        ->where('company_id',session('logged_session_data.company_id'))
                        ->delete();
                    $currentCount = 0;
                } else {
                    DB::table('sys_purchase_grn_license_key')
                        ->where('item_id',$request->item_id)
                        ->where('grn_id',$request->grn_id)
                        ->where('type',1)
                        ->where('company_id',session('logged_session_data.company_id'))
                        ->whereNotIn('license_key', array_values($submittedKeys))
                        ->delete();
                    $currentCount = count($submittedKeys);
                }

                // Upsert submitted keys with exp_date
                $seenInsertKeys = [];
                foreach ($rows as $row) {
                    $key = trim((string)($row['license_key'] ?? ''));
                    if ($key === '') continue;
                    $expDate = SysHelper::normalizeToYmd($row['exp_date'] ?? '');
                    $normalizedKey = mb_strtolower($key);
                    if (isset($seenInsertKeys[$normalizedKey])) continue;
                    $seenInsertKeys[$normalizedKey] = true;

                    $existing = DB::table('sys_purchase_grn_license_key')
                        ->where('item_id',$request->item_id)
                        ->where('grn_id',$request->grn_id)
                        ->where('type',1)
                        ->where('company_id',$companyId)
                        ->where('license_key',$key)
                        ->first();

                    $globalDuplicate = SysPurchaseGrnLicenseKey::where('item_id', $request->item_id)
                        ->where('company_id', $companyId)
                        ->whereRaw('LOWER(license_key) = ?', [$normalizedKey])
                        ->when($existing, function ($q) use ($existing) {
                            $q->where('id', '!=', $existing->id);
                        })
                        ->exists();
                    if ($globalDuplicate) {
                        $duplicates[] = $key;
                        continue;
                    }

                    if ($existing) {
                        DB::table('sys_purchase_grn_license_key')
                            ->where('id', $existing->id)
                            ->update([
                                'exp_date' => $expDate,
                                'updated_by' => Auth::user()->id,
                                'updated_at' => Carbon::now('+04:00'),
                            ]);
                        continue;
                    }

                    $data[] = [
                        'cart_id' => '',
                        'grn_id' => $request->grn_id,
                        'item_id' => $request->item_id,
                        'license_key' => $key,
                        'license_qty' => 1,
                        'exp_date' => $expDate,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                        'company_id' => session('logged_session_data.company_id'),
                        'type' => 1,
                    ];
                }

                if (!empty($data)) {
                    DB::table('sys_purchase_grn_license_key')->insert($data);
                }
            } else {
                $licenseKeys = array_filter(array_unique(array_map('trim', explode(',', $request->license_key))));
                $seen = [];
                $data = [];

                foreach ($licenseKeys as $key) {
                    if ($currentCount >= $maxQty) {
                        break;
                    }
                    if ($key === '') {
                        continue;
                    }
                    $normalizedKey = mb_strtolower($key);
                    if (isset($seen[$normalizedKey])) {
                        continue;
                    }
                    $seen[$normalizedKey] = true;

                    $exists = SysPurchaseGrnLicenseKey::where('item_id',$request->item_id)
                        ->where('grn_id',$request->grn_id)
                        ->where('type',1)
                        ->where('company_id',$companyId)
                        ->where('license_key',$key)
                        ->exists();
                    if ($exists) {
                        continue;
                    }

                    $globalDuplicate = SysPurchaseGrnLicenseKey::where('item_id', $request->item_id)
                        ->where('company_id', $companyId)
                        ->whereRaw('LOWER(license_key) = ?', [$normalizedKey])
                        ->exists();
                    if ($globalDuplicate) {
                        $duplicates[] = $key;
                        continue;
                    }

                    $data[] = [
                        'cart_id' => '',
                        'grn_id' => $request->grn_id,
                        'item_id' => $request->item_id,
                        'license_key' => $key,
                        'license_qty' => 1,
                        'exp_date' => SysHelper::normalizeToYmd($request->exp_date),
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                        'company_id' => session('logged_session_data.company_id'),
                        'type' => 1,
                    ];
                    $currentCount++;
                }

                if (!empty($data)) {
                    DB::table('sys_purchase_grn_license_key')->insert($data);
                }
            }

            $ret = SysPurchaseGrnLicenseKey::where('item_id',$request->item_id)
                ->where('grn_id',$request->grn_id)
                ->where('type',1)
                ->where('company_id',$companyId)
                ->get();
            $response = ['data' => count($ret) > 0 ? $ret->toArray() : []];
            if (!empty($duplicates)) {
                $uniqueDuplicates = array_values(array_unique($duplicates));
                $response['duplicate'] = true;
                $response['duplicate_keys'] = $uniqueDuplicates;
                $response['message'] = 'Duplicate license key(s) already exist: ' . implode(', ', $uniqueDuplicates);
            }
            return json_encode($response);
        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('error'=>$e->getMessage()));
        }
    }
    function view_grn_license_key(Request $request)
    {
        try{
            $ret = SysPurchaseGrnLicenseKey::where('item_id',$request->item_id)->where('grn_id',$request->grn_id)->where('type',1)->where('company_id',session('logged_session_data.company_id'))->get();
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }
        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }
    function delete_grn_license_key(Request $request)
    {
        try{
            DB::table('sys_purchase_grn_license_key')->where('id',$request->id)->where('grn_id',$request->grn_id)->where('type',1)->delete();
            $ret = SysPurchaseGrnLicenseKey::where('item_id',$request->item_id)->where('grn_id',$request->grn_id)->where('type',1)->where('company_id',session('logged_session_data.company_id'))->get();
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }
        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }

    function update_grn_license_key(Request $request)
    {
        try{
            $newKey = trim($request->license_key);
            $expDate = SysHelper::normalizeToYmd($request->exp_date);
            if ($newKey === '') {
                return json_encode(array('error' => 'License key cannot be empty.'));
            }
            // Check duplicate (another row with same key, excluding this id)
            $dup = SysPurchaseGrnLicenseKey::where('item_id', $request->item_id)
                ->where('company_id', session('logged_session_data.company_id'))
                ->whereRaw('LOWER(license_key) = ?', [mb_strtolower($newKey)])
                ->where('id', '!=', $request->id)
                ->count();
            if ($dup > 0) {
                return json_encode(array('error' => 'Duplicate license key already exists.'));
            }
            DB::table('sys_purchase_grn_license_key')
                ->where('id', $request->id)
                ->update(['license_key' => $newKey, 'exp_date' => $expDate]);
            $ret = SysPurchaseGrnLicenseKey::where('item_id',$request->item_id)->where('grn_id',$request->grn_id)->where('type',1)->where('company_id',session('logged_session_data.company_id'))->get();
            return json_encode(array('data' => count($ret) > 0 ? $ret->toArray() : []));
        }catch (\Exception $e) {
            return json_encode(array('error' => $e->getMessage()));
        }
    }

    function import_grn_license_key(Request $request)
    {
        try{
            if ($_FILES['file']['name']) {
                if (!$_FILES['file']['error']) {
                    $name = md5(rand(100, 200));
                    $ext = explode('.', $_FILES['file']['name']);
                    $filename = $name . '.' . $ext[1];
                    $destination = public_path() . '/uploads/product_upload/' . $filename;
                    $location = $_FILES["file"]["tmp_name"];
                    move_uploaded_file($location, $destination);
                    return '/images/' . $filename;
                } else {
                    return 'Ooops!  Your upload triggered the following error:  '.$_FILES['file']['error'];
                }
              }
              else{
                return "e-nofile";
              }



            $selected_file = "";
            if ($request->file('import_file') != "") {
                $file = $request->file('import_file');
                $selected_file = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/product_upload/', $selected_file);
                $selected_file = 'public/uploads/product_upload/' . $selected_file;
                return  $selected_file;
            }
            else { return "nofile"; }

            $objPHPExcel = PHPExcel_IOFactory::load($selected_file);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow();

            $dataArray = $objPHPExcel->getActiveSheet()->toArray();

                for($i=1; $i < count($dataArray); $i++){
                    $data [] = [
                        'cart_id' => '',
                        'grn_id' => $request->grn_id,
                        'item_id' => $request->item_id,
                        $dataArray[0][0] => $dataArray[$i][0],
                        $dataArray[0][1] => $dataArray[$i][1],
                        'license_qty' => 1,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                        'company_id' => session('logged_session_data.company_id'),                        
                    ];
                }

            DB::table('sys_purchase_grn_license_key_test')->insert($data);
            
            $ret = SysPurchaseGrnLicenseKey::select('sys_purchase_grn_license_key.*','grn.doc_number as grn_no','grn.grn_date')
            ->join('sys_purchase_grn as grn','grn.id','sys_purchase_grn_license_key.grn_id')
            ->where('sys_purchase_grn_license_key.item_id',$request->item_id)->where('sys_purchase_grn_license_key.status',1)
            ->where('sys_purchase_grn_license_key.company_id',session('logged_session_data.company_id'))
            ->orderby('exp_date','asc')->get();
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }
        }catch (\Exception $e) {
            //$ret = 'ERROR';
            $ret = $e;
            return json_encode(array('data'=>$ret));
        }
    }
    //  GRN CODE END


    
    //  OPENING STOCK CODE START
    function add_ops_license_key(Request $request)
    {
        try{
            $companyId = session('logged_session_data.company_id');
            $cartId = session('logged_session_data.cart_id');
            $openingStockId = max(0, intval($request->opening_stock_id));
            $scopeOpeningStockId = $openingStockId > 0 ? $openingStockId : -1;
            $scopeCartId = $openingStockId > 0 ? '' : $cartId;
            $maxQty = max(0, intval($request->license_qty));

            if (empty($request->item_id)) {
                return json_encode(array('error' => 'Select a product before adding license keys.'));
            }
            if ($maxQty <= 0) {
                return json_encode(array('error' => 'License quantity must be greater than zero.'));
            }

            $baseQuery = SysPurchaseGrnLicenseKey::where('item_id', $request->item_id)
                ->where('opening_stock_id', $scopeOpeningStockId)
                ->where('type', 2)
                ->where('company_id', $companyId);
            if ($scopeCartId !== '') {
                $baseQuery->where('cart_id', $scopeCartId);
            }

            $existingCount = (clone $baseQuery)->count();
            $expDate = SysHelper::normalizeToYmd($request->exp_date);
            $keys = array_filter(array_unique(array_map('trim', explode(',', (string) $request->license_key))));
            $duplicates = [];

            foreach ($keys as $key) {
                if ($existingCount >= $maxQty) {
                    break;
                }
                if ($key === '') {
                    continue;
                }

                $normalizedKey = mb_strtolower($key);
                $localDuplicate = (clone $baseQuery)
                    ->whereRaw('LOWER(license_key) = ?', [$normalizedKey])
                    ->exists();
                if ($localDuplicate) {
                    $duplicates[] = $key;
                    continue;
                }

                $globalDuplicate = SysPurchaseGrnLicenseKey::where('item_id', $request->item_id)
                    ->where('company_id', $companyId)
                    ->whereRaw('LOWER(license_key) = ?', [$normalizedKey])
                    ->where(function ($q) use ($scopeOpeningStockId, $scopeCartId) {
                        $q->where('type', '!=', 2)
                            ->orWhere('opening_stock_id', '!=', $scopeOpeningStockId);
                        if ($scopeCartId !== '') {
                            $q->orWhere('cart_id', '!=', $scopeCartId);
                        }
                    })
                    ->exists();
                if ($globalDuplicate) {
                    $duplicates[] = $key;
                    continue;
                }

                $data[] = [
                    'cart_id' => $scopeCartId,
                    'opening_stock_id' => $scopeOpeningStockId,
                    'item_id' => $request->item_id,
                    'license_key' => $key,
                    'license_qty' => 1,
                    'exp_date' => $expDate,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                    'company_id' => $companyId,
                    'type' => 2,
                ];
                $existingCount++;
            }

            if (isset($data) && !empty($data)) {
                DB::table('sys_purchase_grn_license_key')->insert($data);
            }

            $ret = (clone $baseQuery)->orderBy('exp_date', 'asc')->orderBy('id', 'asc')->get();

            if ($openingStockId > 0) {
                $opb = DB::table('sys_item_opening_stock')->where('id', $openingStockId)->first();
                if ($opb) {
                    foreach ($ret as $k) {
                        SysHelper::set_license_key_trn(2, $openingStockId, $opb->doc_date, $opb->doc_number, $k->id, $k->item_id, $k->license_key, $k->exp_date);
                    }
                }
            }

            $response = ['data' => count($ret) > 0 ? $ret->toArray() : []];
            if (!empty($duplicates)) {
                $uniqueDuplicates = array_values(array_unique($duplicates));
                $response['duplicate'] = true;
                $response['duplicate_keys'] = $uniqueDuplicates;
                $response['message'] = 'Duplicate license key(s) already exist: ' . implode(', ', $uniqueDuplicates);
            }
            return json_encode($response);
        }catch (\Exception $e) {
            return json_encode(array('error'=>$e->getMessage()));
        }
    }
    function view_ops_license_key(Request $request)
    {
        try{
            $companyId = session('logged_session_data.company_id');
            $cartId = session('logged_session_data.cart_id');
            $openingStockId = max(0, intval($request->opening_stock_id));
            $scopeOpeningStockId = $openingStockId > 0 ? $openingStockId : -1;
            $scopeCartId = $openingStockId > 0 ? '' : $cartId;

            $ret = SysPurchaseGrnLicenseKey::where('item_id', $request->item_id)
                ->where('opening_stock_id', $scopeOpeningStockId)
                ->where('type', 2)
                ->where('company_id', $companyId);
            if ($scopeCartId !== '') {
                $ret->where('cart_id', $scopeCartId);
            }
            $ret = $ret->orderBy('exp_date', 'asc')->orderBy('id', 'asc')->get();
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }
        }catch (\Exception $e) {
            return json_encode(array('error'=>$e->getMessage()));
        }
    }
    function delete_ops_license_key(Request $request)
    {
        try{
            DB::table('sys_purchase_grn_license_key')->where('id',$request->id)->delete();
            DB::table('sys_purchase_grn_license_key_trn')->where('trn_id',$request->id)->delete();
            $companyId = session('logged_session_data.company_id');
            $cartId = session('logged_session_data.cart_id');
            $openingStockId = max(0, intval($request->opening_stock_id));
            $scopeOpeningStockId = $openingStockId > 0 ? $openingStockId : -1;
            $scopeCartId = $openingStockId > 0 ? '' : $cartId;

            $ret = SysPurchaseGrnLicenseKey::where('item_id', $request->item_id)
                ->where('opening_stock_id', $scopeOpeningStockId)
                ->where('type', 2)
                ->where('company_id', $companyId);
            if ($scopeCartId !== '') {
                $ret->where('cart_id', $scopeCartId);
            }
            $ret = $ret->orderBy('exp_date', 'asc')->orderBy('id', 'asc')->get();
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }
        }catch (\Exception $e) {
            return json_encode(array('error'=>$e->getMessage()));
        }
    }

    function update_ops_license_key(Request $request)
    {
        try{
            $companyId = session('logged_session_data.company_id');
            $cartId = session('logged_session_data.cart_id');
            $openingStockId = max(0, intval($request->opening_stock_id));
            $scopeOpeningStockId = $openingStockId > 0 ? $openingStockId : -1;
            $scopeCartId = $openingStockId > 0 ? '' : $cartId;
            $rowId = max(0, intval($request->id));
            $newKey = trim((string) $request->license_key);

            if ($rowId <= 0) {
                return json_encode(array('error' => 'Invalid license key row selected for update.'));
            }
            if ($newKey === '') {
                return json_encode(array('error' => 'License key cannot be empty.'));
            }

            $row = SysPurchaseGrnLicenseKey::where('id', $rowId)
                ->where('item_id', $request->item_id)
                ->where('opening_stock_id', $scopeOpeningStockId)
                ->where('type', 2)
                ->where('company_id', $companyId);
            if ($scopeCartId !== '') {
                $row->where('cart_id', $scopeCartId);
            }
            $row = $row->first();
            if (!$row) {
                return json_encode(array('error' => 'Selected license key was not found.'));
            }

            $normalizedKey = mb_strtolower($newKey);
            $dupInScope = SysPurchaseGrnLicenseKey::where('item_id', $request->item_id)
                ->where('opening_stock_id', $scopeOpeningStockId)
                ->where('type', 2)
                ->where('company_id', $companyId)
                ->whereRaw('LOWER(license_key) = ?', [$normalizedKey])
                ->where('id', '!=', $rowId);
            if ($scopeCartId !== '') {
                $dupInScope->where('cart_id', $scopeCartId);
            }
            if ($dupInScope->exists()) {
                return json_encode(array('error' => 'This license key has already been added.'));
            }

            $globalDuplicate = SysPurchaseGrnLicenseKey::where('item_id', $request->item_id)
                ->where('company_id', $companyId)
                ->whereRaw('LOWER(license_key) = ?', [$normalizedKey])
                ->where('id', '!=', $rowId)
                ->where(function ($q) use ($scopeOpeningStockId, $scopeCartId) {
                    $q->where('type', '!=', 2)
                        ->orWhere('opening_stock_id', '!=', $scopeOpeningStockId);
                    if ($scopeCartId !== '') {
                        $q->orWhere('cart_id', '!=', $scopeCartId);
                    }
                })
                ->exists();
            if ($globalDuplicate) {
                return json_encode(array('error' => 'Duplicate license key(s) already exist: ' . $newKey));
            }

            DB::table('sys_purchase_grn_license_key')
                ->where('id', $rowId)
                ->update([
                    'license_key' => $newKey,
                    'exp_date' => SysHelper::normalizeToYmd($request->exp_date),
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]);

            $ret = SysPurchaseGrnLicenseKey::where('item_id', $request->item_id)
                ->where('opening_stock_id', $scopeOpeningStockId)
                ->where('type', 2)
                ->where('company_id', $companyId);
            if ($scopeCartId !== '') {
                $ret->where('cart_id', $scopeCartId);
            }
            $ret = $ret->orderBy('exp_date', 'asc')->orderBy('id', 'asc')->get();
            return json_encode(array('data' => count($ret) > 0 ? $ret->toArray() : []));
        }catch (\Exception $e) {
            return json_encode(array('error'=>$e->getMessage()));
        }
    }

    function add_ops_license_key_excel(Request $request)
    {
        try{
            $companyId = session('logged_session_data.company_id');
            $cartId = session('logged_session_data.cart_id');
            $openingStockId = max(0, intval($request->opening_stock_id));
            $scopeOpeningStockId = $openingStockId > 0 ? $openingStockId : -1;
            $scopeCartId = $openingStockId > 0 ? '' : $cartId;
            $maxQty = max(0, intval($request->license_qty));

            if (empty($request->item_id)) {
                return json_encode(array('error' => 'Select a product before importing license keys.'));
            }
            if ($maxQty <= 0) {
                return json_encode(array('error' => 'License quantity must be greater than zero before importing.'));
            }
            if (!$request->hasFile('import_file') || !$request->file('import_file')->isValid()) {
                return json_encode(array('error' => 'Select a valid CSV or Excel file to import.'));
            }

            $file = $request->file('import_file');
            $selected_file = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('public/uploads/product_upload/', $selected_file);
            $selected_file = 'public/uploads/product_upload/' . $selected_file;

            $objPHPExcel = PHPExcel_IOFactory::load($selected_file);
            $dataArray = $objPHPExcel->getActiveSheet()->toArray();

            $baseQuery = SysPurchaseGrnLicenseKey::where('item_id', $request->item_id)
                ->where('opening_stock_id', $scopeOpeningStockId)
                ->where('type', 2)
                ->where('company_id', $companyId);
            if ($scopeCartId !== '') {
                $baseQuery->where('cart_id', $scopeCartId);
            }

            $existingCount = (clone $baseQuery)->count();
            $duplicates = [];
            $seenRows = [];
            for ($i = 1; $i < count($dataArray); $i++) {
                if ($existingCount >= $maxQty) {
                    break;
                }

                $licenseKey = trim((string) ($dataArray[$i][0] ?? ''));
                if ($licenseKey === '') {
                    continue;
                }

                $normalizedKey = mb_strtolower($licenseKey);
                if (isset($seenRows[$normalizedKey])) {
                    continue;
                }
                $seenRows[$normalizedKey] = true;

                $localDuplicate = (clone $baseQuery)
                    ->whereRaw('LOWER(license_key) = ?', [$normalizedKey])
                    ->exists();
                if ($localDuplicate) {
                    $duplicates[] = $licenseKey;
                    continue;
                }

                $globalDuplicate = SysPurchaseGrnLicenseKey::where('item_id', $request->item_id)
                    ->where('company_id', $companyId)
                    ->whereRaw('LOWER(license_key) = ?', [$normalizedKey])
                    ->where(function ($q) use ($scopeOpeningStockId, $scopeCartId) {
                        $q->where('type', '!=', 2)
                            ->orWhere('opening_stock_id', '!=', $scopeOpeningStockId);
                        if ($scopeCartId !== '') {
                            $q->orWhere('cart_id', '!=', $scopeCartId);
                        }
                    })
                    ->exists();
                if ($globalDuplicate) {
                    $duplicates[] = $licenseKey;
                    continue;
                }

                $data[] = [
                    'cart_id' => $scopeCartId,
                    'opening_stock_id' => $scopeOpeningStockId,
                    'item_id' => $request->item_id,
                    'license_key' => $licenseKey,
                    'license_qty' => 1,
                    'exp_date' => SysHelper::normalizeToYmd($dataArray[$i][1] ?? ''),
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                    'company_id' => $companyId,
                    'type' => 2,
                ];
                $existingCount++;
            }

            if (isset($data) && !empty($data)) {
                DB::table('sys_purchase_grn_license_key')->insert($data);
            }

            $ret = (clone $baseQuery)->orderBy('exp_date', 'asc')->orderBy('id', 'asc')->get();
            if ($openingStockId > 0) {
                $opb = DB::table('sys_item_opening_stock')->where('id', $openingStockId)->first();
                if ($opb) {
                    foreach ($ret as $k) {
                        SysHelper::set_license_key_trn(2, $openingStockId, $opb->doc_date, $opb->doc_number, $k->id, $k->item_id, $k->license_key, $k->exp_date);
                    }
                }
            }

            $response = ['data' => count($ret) > 0 ? $ret->toArray() : []];
            if (!empty($duplicates)) {
                $uniqueDuplicates = array_values(array_unique($duplicates));
                $response['duplicate'] = true;
                $response['duplicate_keys'] = $uniqueDuplicates;
                $response['message'] = 'Duplicate license key(s) already exist: ' . implode(', ', $uniqueDuplicates);
            }
            return json_encode($response);
        }catch (\Exception $e) {
            return json_encode(array('error'=>$e->getMessage()));
        }
    }
    //  OPENING STOCK CODE END

    //  STOCK IN LICENSE CODE START
    function add_stkin_license_key(Request $request)
    {
        try{
            $companyId = session('logged_session_data.company_id');
            $cartId = session('logged_session_data.cart_id');
            $stockInId = max(0, intval($request->stock_in_id));
            $scopeRefId = $stockInId > 0 ? $stockInId : -1;
            $scopeCartId = $stockInId > 0 ? '' : $cartId;
            $maxQty = max(0, intval($request->license_qty));
            $itemId = $this->resolveLicenseItemId($request);

            if ($itemId <= 0) {
                return json_encode(array('error' => 'Select a valid product before adding license keys.'));
            }
            if ($maxQty <= 0) {
                return json_encode(array('error' => 'License quantity must be greater than zero.'));
            }

            $baseQuery = SysPurchaseGrnLicenseKey::where('item_id', $itemId)
                ->where('opening_stock_id', $scopeRefId)
                ->where('type', 3)
                ->where('company_id', $companyId);
            if ($scopeCartId !== '') {
                $baseQuery->where('cart_id', $scopeCartId);
            }

            $existingCount = (clone $baseQuery)->count();
            $expDate = SysHelper::normalizeToYmd($request->exp_date);
            $keys = array_filter(array_unique(array_map('trim', explode(',', (string) $request->license_key))));
            $duplicates = [];

            foreach ($keys as $key) {
                if ($existingCount >= $maxQty) {
                    break;
                }
                if ($key === '') {
                    continue;
                }

                $normalizedKey = mb_strtolower($key);
                $localDuplicate = (clone $baseQuery)
                    ->whereRaw('LOWER(license_key) = ?', [$normalizedKey])
                    ->exists();
                if ($localDuplicate) {
                    $duplicates[] = $key;
                    continue;
                }

                $globalDuplicate = SysPurchaseGrnLicenseKey::where('item_id', $itemId)
                    ->where('company_id', $companyId)
                    ->whereRaw('LOWER(license_key) = ?', [$normalizedKey])
                    ->where(function ($q) use ($scopeRefId, $scopeCartId) {
                        $q->where('type', '!=', 3)
                            ->orWhere('opening_stock_id', '!=', $scopeRefId);
                        if ($scopeCartId !== '') {
                            $q->orWhere('cart_id', '!=', $scopeCartId);
                        }
                    })
                    ->exists();
                if ($globalDuplicate) {
                    $duplicates[] = $key;
                    continue;
                }

                $data[] = [
                    'cart_id' => $scopeCartId,
                    'opening_stock_id' => $scopeRefId,
                    'item_id' => $itemId,
                    'license_key' => $key,
                    'license_qty' => 1,
                    'exp_date' => $expDate,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                    'company_id' => $companyId,
                    'type' => 3,
                ];
                $existingCount++;
            }

            if (isset($data) && !empty($data)) {
                DB::table('sys_purchase_grn_license_key')->insert($data);
            }

            $ret = (clone $baseQuery)->orderBy('exp_date', 'asc')->orderBy('id', 'asc')->get();
            $response = ['data' => count($ret) > 0 ? $ret->toArray() : []];
            if (!empty($duplicates)) {
                $uniqueDuplicates = array_values(array_unique($duplicates));
                $response['duplicate'] = true;
                $response['duplicate_keys'] = $uniqueDuplicates;
                $response['message'] = 'Duplicate license key(s) already exist: ' . implode(', ', $uniqueDuplicates);
            }
            return json_encode($response);
        }catch (\Exception $e) {
            return json_encode(array('error'=>$e->getMessage()));
        }
    }

    function view_stkin_license_key(Request $request)
    {
        try{
            $companyId = session('logged_session_data.company_id');
            $cartId = session('logged_session_data.cart_id');
            $stockInId = max(0, intval($request->stock_in_id));
            $scopeRefId = $stockInId > 0 ? $stockInId : -1;
            $scopeCartId = $stockInId > 0 ? '' : $cartId;
            $itemId = $this->resolveLicenseItemId($request);

            if ($itemId <= 0) {
                return json_encode(array('data' => []));
            }

            $ret = SysPurchaseGrnLicenseKey::where('item_id', $itemId)
                ->where('opening_stock_id', $scopeRefId)
                ->where('type', 3)
                ->where('company_id', $companyId);
            if ($scopeCartId !== '') {
                $ret->where('cart_id', $scopeCartId);
            }
            $ret = $ret->orderBy('exp_date', 'asc')->orderBy('id', 'asc')->get();
            return json_encode(array('data' => count($ret) > 0 ? $ret : []));
        }catch (\Exception $e) {
            return json_encode(array('error'=>$e->getMessage()));
        }
    }

    function delete_stkin_license_key(Request $request)
    {
        try{
            DB::table('sys_purchase_grn_license_key')->where('id',$request->id)->delete();
            DB::table('sys_purchase_grn_license_key_trn')->where('trn_id',$request->id)->delete();

            $companyId = session('logged_session_data.company_id');
            $cartId = session('logged_session_data.cart_id');
            $stockInId = max(0, intval($request->stock_in_id));
            $scopeRefId = $stockInId > 0 ? $stockInId : -1;
            $scopeCartId = $stockInId > 0 ? '' : $cartId;
            $itemId = $this->resolveLicenseItemId($request);

            $ret = SysPurchaseGrnLicenseKey::where('item_id', $itemId)
                ->where('opening_stock_id', $scopeRefId)
                ->where('type', 3)
                ->where('company_id', $companyId);
            if ($scopeCartId !== '') {
                $ret->where('cart_id', $scopeCartId);
            }
            $ret = $ret->orderBy('exp_date', 'asc')->orderBy('id', 'asc')->get();
            return json_encode(array('data' => count($ret) > 0 ? $ret : []));
        }catch (\Exception $e) {
            return json_encode(array('error'=>$e->getMessage()));
        }
    }

    function update_stkin_license_key(Request $request)
    {
        try{
            $companyId = session('logged_session_data.company_id');
            $cartId = session('logged_session_data.cart_id');
            $stockInId = max(0, intval($request->stock_in_id));
            $scopeRefId = $stockInId > 0 ? $stockInId : -1;
            $scopeCartId = $stockInId > 0 ? '' : $cartId;
            $rowId = max(0, intval($request->id));
            $newKey = trim((string) $request->license_key);
            $itemId = $this->resolveLicenseItemId($request);

            if ($itemId <= 0) {
                return json_encode(array('error' => 'Select a valid product before updating license key.'));
            }
            if ($rowId <= 0) {
                return json_encode(array('error' => 'Invalid license key row selected for update.'));
            }
            if ($newKey === '') {
                return json_encode(array('error' => 'License key cannot be empty.'));
            }

            $row = SysPurchaseGrnLicenseKey::where('id', $rowId)
                ->where('item_id', $itemId)
                ->where('opening_stock_id', $scopeRefId)
                ->where('type', 3)
                ->where('company_id', $companyId);
            if ($scopeCartId !== '') {
                $row->where('cart_id', $scopeCartId);
            }
            $row = $row->first();
            if (!$row) {
                return json_encode(array('error' => 'Selected license key was not found.'));
            }

            $normalizedKey = mb_strtolower($newKey);
            $dupInScope = SysPurchaseGrnLicenseKey::where('item_id', $itemId)
                ->where('opening_stock_id', $scopeRefId)
                ->where('type', 3)
                ->where('company_id', $companyId)
                ->whereRaw('LOWER(license_key) = ?', [$normalizedKey])
                ->where('id', '!=', $rowId);
            if ($scopeCartId !== '') {
                $dupInScope->where('cart_id', $scopeCartId);
            }
            if ($dupInScope->exists()) {
                return json_encode(array('error' => 'This license key has already been added.'));
            }

            $globalDuplicate = SysPurchaseGrnLicenseKey::where('item_id', $itemId)
                ->where('company_id', $companyId)
                ->whereRaw('LOWER(license_key) = ?', [$normalizedKey])
                ->where('id', '!=', $rowId)
                ->where(function ($q) use ($scopeRefId, $scopeCartId) {
                    $q->where('type', '!=', 3)
                        ->orWhere('opening_stock_id', '!=', $scopeRefId);
                    if ($scopeCartId !== '') {
                        $q->orWhere('cart_id', '!=', $scopeCartId);
                    }
                })
                ->exists();
            if ($globalDuplicate) {
                return json_encode(array('error' => 'Duplicate license key(s) already exist: ' . $newKey));
            }

            DB::table('sys_purchase_grn_license_key')
                ->where('id', $rowId)
                ->update([
                    'license_key' => $newKey,
                    'exp_date' => SysHelper::normalizeToYmd($request->exp_date),
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]);

            $ret = SysPurchaseGrnLicenseKey::where('item_id', $itemId)
                ->where('opening_stock_id', $scopeRefId)
                ->where('type', 3)
                ->where('company_id', $companyId);
            if ($scopeCartId !== '') {
                $ret->where('cart_id', $scopeCartId);
            }
            $ret = $ret->orderBy('exp_date', 'asc')->orderBy('id', 'asc')->get();
            return json_encode(array('data' => count($ret) > 0 ? $ret->toArray() : []));
        }catch (\Exception $e) {
            return json_encode(array('error'=>$e->getMessage()));
        }
    }

    function add_stkin_license_key_excel(Request $request)
    {
        try{
            $companyId = session('logged_session_data.company_id');
            $cartId = session('logged_session_data.cart_id');
            $stockInId = max(0, intval($request->stock_in_id));
            $scopeRefId = $stockInId > 0 ? $stockInId : -1;
            $scopeCartId = $stockInId > 0 ? '' : $cartId;
            $maxQty = max(0, intval($request->license_qty));
            $itemId = $this->resolveLicenseItemId($request);

            if ($itemId <= 0) {
                return json_encode(array('error' => 'Select a valid product before importing license keys.'));
            }
            if ($maxQty <= 0) {
                return json_encode(array('error' => 'License quantity must be greater than zero before importing.'));
            }
            if (!$request->hasFile('import_file') || !$request->file('import_file')->isValid()) {
                return json_encode(array('error' => 'Select a valid CSV or Excel file to import.'));
            }

            $file = $request->file('import_file');
            $selected_file = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('public/uploads/product_upload/', $selected_file);
            $selected_file = 'public/uploads/product_upload/' . $selected_file;

            $objPHPExcel = PHPExcel_IOFactory::load($selected_file);
            $dataArray = $objPHPExcel->getActiveSheet()->toArray();

            $baseQuery = SysPurchaseGrnLicenseKey::where('item_id', $itemId)
                ->where('opening_stock_id', $scopeRefId)
                ->where('type', 3)
                ->where('company_id', $companyId);
            if ($scopeCartId !== '') {
                $baseQuery->where('cart_id', $scopeCartId);
            }

            $existingCount = (clone $baseQuery)->count();
            $duplicates = [];
            $seenRows = [];
            for ($i = 1; $i < count($dataArray); $i++) {
                if ($existingCount >= $maxQty) {
                    break;
                }

                $licenseKey = trim((string) ($dataArray[$i][0] ?? ''));
                if ($licenseKey === '') {
                    continue;
                }

                $normalizedKey = mb_strtolower($licenseKey);
                if (isset($seenRows[$normalizedKey])) {
                    continue;
                }
                $seenRows[$normalizedKey] = true;

                $localDuplicate = (clone $baseQuery)
                    ->whereRaw('LOWER(license_key) = ?', [$normalizedKey])
                    ->exists();
                if ($localDuplicate) {
                    $duplicates[] = $licenseKey;
                    continue;
                }

                $globalDuplicate = SysPurchaseGrnLicenseKey::where('item_id', $itemId)
                    ->where('company_id', $companyId)
                    ->whereRaw('LOWER(license_key) = ?', [$normalizedKey])
                    ->where(function ($q) use ($scopeRefId, $scopeCartId) {
                        $q->where('type', '!=', 3)
                            ->orWhere('opening_stock_id', '!=', $scopeRefId);
                        if ($scopeCartId !== '') {
                            $q->orWhere('cart_id', '!=', $scopeCartId);
                        }
                    })
                    ->exists();
                if ($globalDuplicate) {
                    $duplicates[] = $licenseKey;
                    continue;
                }

                $data[] = [
                    'cart_id' => $scopeCartId,
                    'opening_stock_id' => $scopeRefId,
                    'item_id' => $itemId,
                    'license_key' => $licenseKey,
                    'license_qty' => 1,
                    'exp_date' => SysHelper::normalizeToYmd($dataArray[$i][1] ?? ''),
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                    'company_id' => $companyId,
                    'type' => 3,
                ];
                $existingCount++;
            }

            if (isset($data) && !empty($data)) {
                DB::table('sys_purchase_grn_license_key')->insert($data);
            }

            $ret = (clone $baseQuery)->orderBy('exp_date', 'asc')->orderBy('id', 'asc')->get();
            $response = ['data' => count($ret) > 0 ? $ret->toArray() : []];
            if (!empty($duplicates)) {
                $uniqueDuplicates = array_values(array_unique($duplicates));
                $response['duplicate'] = true;
                $response['duplicate_keys'] = $uniqueDuplicates;
                $response['message'] = 'Duplicate license key(s) already exist: ' . implode(', ', $uniqueDuplicates);
            }
            return json_encode($response);
        }catch (\Exception $e) {
            return json_encode(array('error'=>$e->getMessage()));
        }
    }
    //  STOCK IN LICENSE CODE END


    
    //  SALES RETURN CODE START
    
    function sales_return_get_dn_license_key(Request $request)
    {
        try{
            $dnid = SysDeliveryNote::where('doc_number',$request->dn_doc_number)->value('id');
            $companyId = session('logged_session_data.company_id');
            $cartId = session('logged_session_data.cart_id');
            $srId = max(0, intval($request->sales_return_id));
            $ret = SysPurchaseGrnLicenseKey::select('sys_purchase_grn_license_key.*')
            ->where('sys_purchase_grn_license_key.item_id',$request->item_id)->where('sys_purchase_grn_license_key.status',2)
            ->where('sys_purchase_grn_license_key.dn_id',$dnid)
            ->where('sys_purchase_grn_license_key.company_id',$companyId)
            ->where(function ($q) use ($srId, $cartId) {
                if ($srId > 0) {
                    $q->where('sys_purchase_grn_license_key.sales_return_id', $srId)
                        ->orWhere(function ($free) {
                            $free->whereNull('sys_purchase_grn_license_key.sales_return_id')
                                ->orWhere('sys_purchase_grn_license_key.sales_return_id', 0);
                        })
                        ->orWhere(function ($draft) use ($cartId) {
                            $draft->where('sys_purchase_grn_license_key.sales_return_id', -1)
                                ->where('sys_purchase_grn_license_key.cart_id', $cartId);
                        });
                } else {
                    $q->where(function ($free) {
                        $free->whereNull('sys_purchase_grn_license_key.sales_return_id')
                            ->orWhere('sys_purchase_grn_license_key.sales_return_id', 0);
                    })->orWhere(function ($draft) use ($cartId) {
                        $draft->where('sys_purchase_grn_license_key.sales_return_id', -1)
                            ->where('sys_purchase_grn_license_key.cart_id', $cartId);
                    });
                }
            })
            ->orderby('exp_date','asc')->get();
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }
        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$e));
        }
    }
    function sales_return_update_dn_license_key(Request $request)
    {
        try{
            $ids = explode(',', $request->id);
            $dt = SysPurchaseGrnLicenseKey::wherein('id',$ids)->where('status',2)
            ->where('company_id',session('logged_session_data.company_id'))
            ->orderby('exp_date','asc')->get();

            if(count($dt) > 0){
                SysPurchaseGrnLicenseKey::where(['cart_id' => session('logged_session_data.cart_id'),
                        'sales_return_id' => '-1',])->update([
                        'sales_return_id' => 0, 'updated_by' => Auth::user()->id,
                        'updated_at' => Carbon::now('+04:00')]);
                foreach($dt as $d){                    
                    SysPurchaseGrnLicenseKey::where('id',$d->id)->update([
                        'cart_id' => session('logged_session_data.cart_id'),
                        'sales_return_id' => '-1',
                        'updated_by' => Auth::user()->id,
                        'updated_at' => Carbon::now('+04:00')]);
                }
            }
            
            $ret = [];
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }
        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }

    function sales_return_get_license_key(Request $request)
    {
        try{
            $ret = SysPurchaseGrnLicenseKey::select('sys_purchase_grn_license_key.*')
            ->where('sys_purchase_grn_license_key.item_id',$request->item_id)->where('sys_purchase_grn_license_key.status',1)->where('sales_return_id',$request->sales_return_id)
            ->where('sys_purchase_grn_license_key.company_id',session('logged_session_data.company_id'))
            ->orderby('exp_date','asc')->get();
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }
        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }
    function sales_return_update_license_key(Request $request)
    {
        try{
            SysPurchaseGrnLicenseKey::where('item_id',$request->item_id)->where('cart_id',session('logged_session_data.cart_id'))->delete();
            
            $ids = explode(',', $request->id);
            $dt = SysPurchaseGrnLicenseKey::wherein('id',$ids)->where('status',1)
            ->where('company_id',session('logged_session_data.company_id'))
            ->orderby('exp_date','asc')->get();

            foreach($dt as $d){
                $data[] = [
                    'cart_id' => session('logged_session_data.cart_id'),
                    'dn_id' => 0,
                    'grn_id' => $d->grn_id,
                    'item_id' => $d->item_id,
                    'license_key' => $d->license_key,
                    'license_qty' => $d->license_qty,
                    'exp_date' => $d->exp_date,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                    'company_id' => session('logged_session_data.company_id'),
                ];
            }
            DB::table('sys_purchase_dln_license_key')->insert($data);
            
            $ret = [];
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }
        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }
    //  SALES RETURN CODE END


    
    //  DELIVERY NOTE CODE START
    
    //for delivery note page
    function dn_get_grn_license_key(Request $request)
    {
        try{
            $ret = SysPurchaseGrnLicenseKey::select(
                'sys_purchase_grn_license_key.*',
                'grn.doc_number as grn_no',
                'grn.grn_date',
                'grn.bill_number as grn_bill_number',
                'grn.deal_id as grn_deal_id',
                'vendor.account_name as supplier_name',
                'ops.doc_number as ops_doc_number',
                'ops.doc_date as ops_doc_date',
                'stkin.doc_number as stkin_doc_number',
                'stkin.date as stkin_doc_date',
                'sr.doc_number as sr_doc_number',
                'sr.doc_date as sr_doc_date',
                'sr.deal_id as sr_deal_id',
                'sr.lpo_number as sr_lpo_number',
                'customer.account_name as sr_customer_name',
                'sr_deal.code as sr_deal_code',
                'grn_deal.code as grn_deal_code'
            )
            ->leftJoin('sys_purchase_grn as grn', 'grn.id', '=', 'sys_purchase_grn_license_key.grn_id')
            ->leftJoin('sys_chartofaccounts as vendor', 'vendor.id', '=', 'grn.vendors')
            ->leftJoin('sys_item_opening_stock as ops', function ($join) {
                $join->on('ops.id', '=', 'sys_purchase_grn_license_key.opening_stock_id')
                    ->where('sys_purchase_grn_license_key.type', 2);
            })
            ->leftJoin('sys_stock_in as stkin', function ($join) {
                $join->on('stkin.id', '=', 'sys_purchase_grn_license_key.opening_stock_id')
                    ->where('sys_purchase_grn_license_key.type', 3);
            })
            ->leftJoin('sys_sales_return as sr', 'sr.id', '=', 'sys_purchase_grn_license_key.sales_return_id')
            ->leftJoin('sys_chartofaccounts as customer', 'customer.id', '=', 'sr.customer')
            ->leftJoin('sys_crm_deals as sr_deal', 'sr_deal.id', '=', 'sr.deal_id')
            ->leftJoin('sys_crm_deals as grn_deal', 'grn_deal.id', '=', 'grn.deal_id')
            ->where('sys_purchase_grn_license_key.item_id',$request->item_id)
            ->where('sys_purchase_grn_license_key.status',1)
            ->where('sys_purchase_grn_license_key.company_id',session('logged_session_data.company_id'))
            ->where(function($q){
                $q->where('sys_purchase_grn_license_key.dn_id', 0)
                  ->orWhere(function($q2){
                      $q2->where('sys_purchase_grn_license_key.dn_id', -1)
                         ->where('sys_purchase_grn_license_key.cart_id', session('logged_session_data.cart_id'));
                  });
            })
            ->where(function($q){
                $q->whereNull('sys_purchase_grn_license_key.purchase_return_id')
                  ->orWhere('sys_purchase_grn_license_key.purchase_return_id', 0);
            })
            ->when($this->licenseKeyHasColumn('stock_out_id'), function ($q) {
                $q->whereRaw('COALESCE(sys_purchase_grn_license_key.stock_out_id,0)=0');
            })
            ->orderBy('sys_purchase_grn_license_key.exp_date','asc')
            ->orderByRaw("COALESCE(grn.grn_date, '9999-12-31') asc")
            ->orderBy('sys_purchase_grn_license_key.id','asc')
            ->get();
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }
        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$e));
        }
    }
    function dn_update_grn_license_key(Request $request)
    {
        try{
            $qtyLimit = max(0, intval($request->qty_limit));
            $itemId = $request->item_id;
            $companyId = session('logged_session_data.company_id');
            $cartId = session('logged_session_data.cart_id');

            $stagingIds = collect(explode(',', (string) $request->id))
                ->map(function ($id) {
                    return intval(trim($id));
                })
                ->filter(function ($id) {
                    return $id > 0;
                })
                ->unique()
                ->values()
                ->all();

            $dnEditId = max(0, intval($request->dn_id));
            $keepDnKeyIds = collect(explode(',', (string) $request->keep_dn_key_ids))
                ->map(function ($id) {
                    return intval(trim($id));
                })
                ->filter(function ($id) {
                    return $id > 0;
                })
                ->unique()
                ->values()
                ->all();

            $totalKeysSelected = count($stagingIds) + count($keepDnKeyIds);
            if ($qtyLimit > 0 && $totalKeysSelected > $qtyLimit) {
                return json_encode(array('error' => 'Only '.$qtyLimit.' license keys can be selected for this item quantity.'));
            }

            // Editing a saved DN: return keys to stock when unchecked (status 2 → 1, dn cleared).
            if ($dnEditId > 0) {
                $validatedKeep = SysPurchaseGrnLicenseKey::whereIn('id', $keepDnKeyIds)
                    ->where('item_id', $itemId)
                    ->where('dn_id', $dnEditId)
                    ->where('status', 2)
                    ->where('company_id', $companyId)
                    ->pluck('id')
                    ->all();

                $assignedOnDn = SysPurchaseGrnLicenseKey::where('item_id', $itemId)
                    ->where('dn_id', $dnEditId)
                    ->where('status', 2)
                    ->where('company_id', $companyId)
                    ->pluck('id')
                    ->all();

                $toRelease = array_diff($assignedOnDn, $validatedKeep);
                foreach ($toRelease as $releaseId) {
                    SysPurchaseGrnLicenseKey::where('id', $releaseId)->update([
                        'status' => 1,
                        'dn_id' => 0,
                        'cart_id' => '',
                        'updated_by' => Auth::user()->id,
                        'updated_at' => Carbon::now('+04:00'),
                    ]);
                    DB::table('sys_purchase_grn_license_key_trn')
                        ->where('type', 4)
                        ->where('trn_id', $dnEditId)
                        ->where('key_id', $releaseId)
                        ->delete();
                }
            }

            // Clear draft reservations (dn_id = -1) when no new stock keys are staged.
            if (count($stagingIds) === 0) {
                SysPurchaseGrnLicenseKey::where([
                    'cart_id' => $cartId,
                    'dn_id' => '-1',
                    'item_id' => $itemId,
                ])->update([
                    'dn_id' => 0,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]);
                return json_encode(array('data' => []));
            }

            $dt = SysPurchaseGrnLicenseKey::whereIn('id', $stagingIds)
                ->where('item_id', $itemId)
                ->where('status', 1)
                ->where('company_id', $companyId)
                ->where(function ($q) use ($cartId) {
                    $q->where('dn_id', 0)
                        ->orWhere(function ($q2) use ($cartId) {
                            $q2->where('dn_id', -1)
                                ->where('cart_id', $cartId);
                        });
                })
                ->orderBy('exp_date', 'asc')
                ->get();

            if (count($dt) > 0) {
                SysPurchaseGrnLicenseKey::where([
                    'cart_id' => $cartId,
                    'dn_id' => '-1',
                    'item_id' => $itemId,
                ])->update([
                    'dn_id' => 0,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]);
                foreach ($dt as $d) {
                    SysPurchaseGrnLicenseKey::where('id', $d->id)->update([
                        'cart_id' => $cartId,
                        'dn_id' => '-1',
                        'updated_by' => Auth::user()->id,
                        'updated_at' => Carbon::now('+04:00'),
                    ]);
                }
            }

            return json_encode(array('data' => []));
        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$e));
        }
    }

    //for delivery edit note page
    function dn_get_dln_license_key(Request $request)
    {
        try{
            $dnId = intval($request->dn_id);
            $cartId = session('logged_session_data.cart_id');
            $companyId = session('logged_session_data.company_id');

            // Keys on this DN plus available stock for the item (same pool as DN add).
            $ret = SysPurchaseGrnLicenseKey::select(
                'sys_purchase_grn_license_key.*',
                'grn.doc_number as grn_no',
                'grn.grn_date',
                'grn.bill_number as grn_bill_number',
                'grn.deal_id as grn_deal_id',
                'vendor.account_name as supplier_name',
                'ops.doc_number as ops_doc_number',
                'ops.doc_date as ops_doc_date',
                'stkin.doc_number as stkin_doc_number',
                'stkin.date as stkin_doc_date',
                'sr.doc_number as sr_doc_number',
                'sr.doc_date as sr_doc_date',
                'sr.deal_id as sr_deal_id',
                'sr.lpo_number as sr_lpo_number',
                'customer.account_name as sr_customer_name',
                'sr_deal.code as sr_deal_code',
                'grn_deal.code as grn_deal_code'
            )
            ->leftJoin('sys_purchase_grn as grn', 'grn.id', '=', 'sys_purchase_grn_license_key.grn_id')
            ->leftJoin('sys_chartofaccounts as vendor', 'vendor.id', '=', 'grn.vendors')
            ->leftJoin('sys_item_opening_stock as ops', function ($join) {
                $join->on('ops.id', '=', 'sys_purchase_grn_license_key.opening_stock_id')
                    ->where('sys_purchase_grn_license_key.type', 2);
            })
            ->leftJoin('sys_stock_in as stkin', function ($join) {
                $join->on('stkin.id', '=', 'sys_purchase_grn_license_key.opening_stock_id')
                    ->where('sys_purchase_grn_license_key.type', 3);
            })
            ->leftJoin('sys_sales_return as sr', 'sr.id', '=', 'sys_purchase_grn_license_key.sales_return_id')
            ->leftJoin('sys_chartofaccounts as customer', 'customer.id', '=', 'sr.customer')
            ->leftJoin('sys_crm_deals as sr_deal', 'sr_deal.id', '=', 'sr.deal_id')
            ->leftJoin('sys_crm_deals as grn_deal', 'grn_deal.id', '=', 'grn.deal_id')
            ->where('sys_purchase_grn_license_key.item_id', $request->item_id)
            ->where('sys_purchase_grn_license_key.company_id', $companyId)
            ->where(function ($q) use ($dnId, $cartId) {
                $q->where(function ($onDln) use ($dnId) {
                    $onDln->where('sys_purchase_grn_license_key.status', 2)
                        ->where('sys_purchase_grn_license_key.dn_id', $dnId);
                })->orWhere(function ($available) use ($cartId) {
                    $available->where('sys_purchase_grn_license_key.status', 1)
                        ->where(function ($pool) use ($cartId) {
                            $pool->where('sys_purchase_grn_license_key.dn_id', 0)
                                ->orWhere(function ($draft) use ($cartId) {
                                    $draft->where('sys_purchase_grn_license_key.dn_id', -1)
                                        ->where('sys_purchase_grn_license_key.cart_id', $cartId);
                                });
                        })
                        ->where(function($q){
                            $q->whereNull('sys_purchase_grn_license_key.purchase_return_id')
                              ->orWhere('sys_purchase_grn_license_key.purchase_return_id', 0);
                        })
                        ->when($this->licenseKeyHasColumn('stock_out_id'), function ($q) {
                            $q->whereRaw('COALESCE(sys_purchase_grn_license_key.stock_out_id,0)=0');
                        });
                });
            })
            ->orderByRaw('CASE WHEN sys_purchase_grn_license_key.status = 2 THEN 0 ELSE 1 END')
            ->orderBy('sys_purchase_grn_license_key.exp_date', 'asc')
            ->orderByRaw("COALESCE(grn.grn_date, '9999-12-31') asc")
            ->orderBy('sys_purchase_grn_license_key.id', 'asc')
            ->get();
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }
        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }
    function dn_update_dln_license_key(Request $request)
    {
        try{
            SysPurchaseGrnLicenseKey::where('item_id',$request->item_id)->where('cart_id',session('logged_session_data.cart_id'))->delete();
            
            $ids = explode(',', $request->id);
            $dt = SysPurchaseGrnLicenseKey::wherein('id',$ids)->where('status',1)
            ->where('company_id',session('logged_session_data.company_id'))
            ->orderby('exp_date','asc')->get();

            foreach($dt as $d){
                $data[] = [
                    'cart_id' => session('logged_session_data.cart_id'),
                    'dn_id' => 0,
                    'grn_id' => $d->grn_id,
                    'item_id' => $d->item_id,
                    'license_key' => $d->license_key,
                    'license_qty' => $d->license_qty,
                    'exp_date' => $d->exp_date,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                    'company_id' => session('logged_session_data.company_id'),
                ];
            }
            DB::table('sys_purchase_dln_license_key')->insert($data);
            
            $ret = [];
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }
        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }
    //  DELIVERY NOTE CODE END


    
    //  PURCHASE RETURN CODE START

    /**
     * Available license keys for a new purchase return line, plus draft selections for this session.
     * Pass pr_id when editing an existing purchase return (same pattern as DN edit).
     */
    function purchase_return_get_grn_license_key(Request $request)
    {
        try {
            $companyId = session('logged_session_data.company_id');
            $cartId = session('logged_session_data.cart_id');
            $itemId = $request->item_id;
            $prId = max(0, intval($request->pr_id));

            $base = SysPurchaseGrnLicenseKey::select(
                'sys_purchase_grn_license_key.*',
                'grn.doc_number as grn_no',
                'grn.grn_date',
                'grn.bill_number as grn_bill_number',
                'grn.deal_id as grn_deal_id',
                'vendor.account_name as supplier_name',
                'ops.doc_number as ops_doc_number',
                'ops.doc_date as ops_doc_date',
                'stkin.doc_number as stkin_doc_number',
                'stkin.date as stkin_doc_date',
                'sr.doc_number as sr_doc_number',
                'sr.doc_date as sr_doc_date',
                'sr.deal_id as sr_deal_id',
                'sr.lpo_number as sr_lpo_number',
                'customer.account_name as sr_customer_name',
                'sr_deal.code as sr_deal_code',
                'grn_deal.code as grn_deal_code'
            )
                ->leftJoin('sys_purchase_grn as grn', 'grn.id', '=', 'sys_purchase_grn_license_key.grn_id')
                ->leftJoin('sys_chartofaccounts as vendor', 'vendor.id', '=', 'grn.vendors')
                ->leftJoin('sys_item_opening_stock as ops', function ($join) {
                    $join->on('ops.id', '=', 'sys_purchase_grn_license_key.opening_stock_id')
                        ->where('sys_purchase_grn_license_key.type', 2);
                })
                ->leftJoin('sys_stock_in as stkin', function ($join) {
                    $join->on('stkin.id', '=', 'sys_purchase_grn_license_key.opening_stock_id')
                        ->where('sys_purchase_grn_license_key.type', 3);
                })
                ->leftJoin('sys_sales_return as sr', 'sr.id', '=', 'sys_purchase_grn_license_key.sales_return_id')
                ->leftJoin('sys_chartofaccounts as customer', 'customer.id', '=', 'sr.customer')
                ->leftJoin('sys_crm_deals as sr_deal', 'sr_deal.id', '=', 'sr.deal_id')
                ->leftJoin('sys_crm_deals as grn_deal', 'grn_deal.id', '=', 'grn.deal_id')
                ->where('sys_purchase_grn_license_key.item_id', $itemId)
                ->where('sys_purchase_grn_license_key.company_id', $companyId);

            if ($prId > 0) {
                $ret = (clone $base)->where(function ($q) use ($prId, $cartId) {
                    $q->where(function ($onPr) use ($prId) {
                        $onPr->where('sys_purchase_grn_license_key.status', 2)
                            ->where('sys_purchase_grn_license_key.purchase_return_id', $prId);
                    })->orWhere(function ($available) use ($cartId) {
                        $available->where('sys_purchase_grn_license_key.status', 1)
                            ->where(function ($pool) use ($cartId) {
                                $pool->where(function ($dnFree) {
                                    $dnFree->where('sys_purchase_grn_license_key.dn_id', 0)
                                        ->orWhereNull('sys_purchase_grn_license_key.dn_id');
                                })
                                    ->where(function ($prFree) {
                                        $prFree->where('sys_purchase_grn_license_key.purchase_return_id', 0)
                                            ->orWhereNull('sys_purchase_grn_license_key.purchase_return_id');
                                    })->when($this->licenseKeyHasColumn('stock_out_id'), function ($q) {
                                        $q->whereRaw('COALESCE(sys_purchase_grn_license_key.stock_out_id,0) = 0');
                                    });
                            })
                            ->orWhere(function ($draft) use ($cartId) {
                                $draft->where('sys_purchase_grn_license_key.purchase_return_id', -1)
                                    ->where('sys_purchase_grn_license_key.cart_id', $cartId);
                            });
                    });
                })
                    ->orderByRaw('CASE WHEN sys_purchase_grn_license_key.status = 2 THEN 0 ELSE 1 END')
                    ->orderBy('sys_purchase_grn_license_key.exp_date', 'asc')
                    ->orderByRaw("COALESCE(grn.grn_date, '9999-12-31') asc")
                    ->orderBy('sys_purchase_grn_license_key.id', 'asc')
                    ->get();
            } else {
                $ret = $base->where('sys_purchase_grn_license_key.status', 1)
                    ->where(function ($q) use ($cartId) {
                        $q->where(function ($pool) use ($cartId) {
                            $pool->where(function ($dnFree) {
                                $dnFree->where('sys_purchase_grn_license_key.dn_id', 0)
                                    ->orWhereNull('sys_purchase_grn_license_key.dn_id');
                            })
                                ->where(function ($prFree) {
                                    $prFree->where('sys_purchase_grn_license_key.purchase_return_id', 0)
                                        ->orWhereNull('sys_purchase_grn_license_key.purchase_return_id');
                                })->when($this->licenseKeyHasColumn('stock_out_id'), function ($q) {
                                    $q->whereRaw('COALESCE(sys_purchase_grn_license_key.stock_out_id,0) = 0');
                                });
                        })->orWhere(function ($draft) use ($cartId) {
                            $draft->where('sys_purchase_grn_license_key.purchase_return_id', -1)
                                ->where('sys_purchase_grn_license_key.cart_id', $cartId);
                        });
                    })
                    ->orderBy('sys_purchase_grn_license_key.exp_date', 'asc')
                    ->orderByRaw("COALESCE(grn.grn_date, '9999-12-31') asc")
                    ->orderBy('sys_purchase_grn_license_key.id', 'asc')
                    ->get();
            }

            return json_encode(array('data' => count($ret) > 0 ? $ret : []));
        } catch (\Exception $e) {
            return json_encode(array('data' => 'ERROR'));
        }
    }

    /**
     * Stage / adjust license keys for purchase return (add + edit), mirroring dn_update_grn_license_key.
     */
    function purchase_return_update_grn_license_key(Request $request)
    {
        try {
            $qtyLimit = max(0, intval($request->qty_limit));
            $itemId = $request->item_id;
            $companyId = session('logged_session_data.company_id');
            $cartId = session('logged_session_data.cart_id');

            $stagingIds = collect(explode(',', (string) $request->id))
                ->map(function ($id) {
                    return intval(trim($id));
                })
                ->filter(function ($id) {
                    return $id > 0;
                })
                ->unique()
                ->values()
                ->all();

            $prEditId = max(0, intval($request->pr_id));
            $keepPrKeyIds = collect(explode(',', (string) $request->keep_pr_key_ids))
                ->map(function ($id) {
                    return intval(trim($id));
                })
                ->filter(function ($id) {
                    return $id > 0;
                })
                ->unique()
                ->values()
                ->all();

            $totalKeysSelected = count($stagingIds) + count($keepPrKeyIds);
            if ($qtyLimit > 0 && $totalKeysSelected > $qtyLimit) {
                return json_encode(array('error' => 'Only '.$qtyLimit.' license keys can be selected for this item quantity.'));
            }

            if ($prEditId > 0) {
                $validatedKeep = SysPurchaseGrnLicenseKey::whereIn('id', $keepPrKeyIds)
                    ->where('item_id', $itemId)
                    ->where('purchase_return_id', $prEditId)
                    ->where('status', 2)
                    ->where('company_id', $companyId)
                    ->pluck('id')
                    ->all();

                $assignedOnPr = SysPurchaseGrnLicenseKey::where('item_id', $itemId)
                    ->where('purchase_return_id', $prEditId)
                    ->where('status', 2)
                    ->where('company_id', $companyId)
                    ->pluck('id')
                    ->all();

                $toRelease = array_diff($assignedOnPr, $validatedKeep);
                foreach ($toRelease as $releaseId) {
                    SysPurchaseGrnLicenseKey::where('id', $releaseId)->update([
                        'status' => 1,
                        'purchase_return_id' => 0,
                        'cart_id' => '',
                        'updated_by' => Auth::user()->id,
                        'updated_at' => Carbon::now('+04:00'),
                    ]);
                    DB::table('sys_purchase_grn_license_key_trn')
                        ->where('type', 5)
                        ->where('trn_id', $prEditId)
                        ->where('key_id', $releaseId)
                        ->delete();
                }
            }

            if (count($stagingIds) === 0) {
                SysPurchaseGrnLicenseKey::where([
                    'cart_id' => $cartId,
                    'purchase_return_id' => '-1',
                    'item_id' => $itemId,
                ])->update([
                    'purchase_return_id' => 0,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]);

                return json_encode(array('data' => []));
            }

            $dt = SysPurchaseGrnLicenseKey::whereIn('id', $stagingIds)
                ->where('item_id', $itemId)
                ->where('status', 1)
                ->where('company_id', $companyId)
                ->where(function ($q) use ($cartId) {
                    $q->where(function ($pool) {
                        $pool->where(function ($dnFree) {
                            $dnFree->where('dn_id', 0)->orWhereNull('dn_id');
                        })
                            ->where(function ($prFree) {
                                $prFree->where('purchase_return_id', 0)->orWhereNull('purchase_return_id');
                            })->when($this->licenseKeyHasColumn('stock_out_id'), function ($q) {
                                $q->whereRaw('COALESCE(stock_out_id,0) = 0');
                            });
                    })->orWhere(function ($draft) use ($cartId) {
                        $draft->where('purchase_return_id', -1)
                            ->where('cart_id', $cartId);
                    });
                })
                ->orderBy('exp_date', 'asc')
                ->get();

            if (count($dt) > 0) {
                SysPurchaseGrnLicenseKey::where([
                    'cart_id' => $cartId,
                    'purchase_return_id' => '-1',
                    'item_id' => $itemId,
                ])->update([
                    'purchase_return_id' => 0,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]);
                foreach ($dt as $d) {
                    SysPurchaseGrnLicenseKey::where('id', $d->id)->update([
                        'cart_id' => $cartId,
                        'purchase_return_id' => '-1',
                        'updated_by' => Auth::user()->id,
                        'updated_at' => Carbon::now('+04:00'),
                    ]);
                }
            }

            return json_encode(array('data' => []));
        } catch (\Exception $e) {
            return json_encode(array('data' => 'ERROR'));
        }
    }

    /** @deprecated Use purchase_return_get_grn_license_key with pr_id */
    function purchase_return_get_license_key(Request $request)
    {
        $request->merge(['pr_id' => $request->purchase_return_id]);

        return $this->purchase_return_get_grn_license_key($request);
    }

    /** @deprecated Use purchase_return_update_grn_license_key */
    function purchase_return_update_license_key(Request $request)
    {
        return $this->purchase_return_update_grn_license_key($request);
    }
    //  PURCHASE RETURN CODE END

    //  STOCK OUT LICENSE (same lifecycle as DN: status 1 in stock -> staged stock_out_id -1 -> status 2 on post)
    function stock_out_get_grn_license_key(Request $request)
    {
        try {
            if (!$this->licenseKeyHasColumn('stock_out_id')) {
                return json_encode(array('data' => [], 'error' => 'stock_out_id column is missing in sys_purchase_grn_license_key.'));
            }
            $companyId = session('logged_session_data.company_id');
            $cartId = session('logged_session_data.cart_id');
            $itemId = $request->item_id;
            $outId = max(0, intval($request->stock_out_id));

            $base = SysPurchaseGrnLicenseKey::select(
                'sys_purchase_grn_license_key.*',
                'grn.doc_number as grn_no',
                'grn.grn_date',
                'grn.bill_number as grn_bill_number',
                'grn.deal_id as grn_deal_id',
                'vendor.account_name as supplier_name',
                'ops.doc_number as ops_doc_number',
                'ops.doc_date as ops_doc_date',
                'stkin.doc_number as stkin_doc_number',
                'stkin.date as stkin_doc_date',
                'sr.doc_number as sr_doc_number',
                'sr.doc_date as sr_doc_date',
                'sr.deal_id as sr_deal_id',
                'sr.lpo_number as sr_lpo_number',
                'customer.account_name as sr_customer_name',
                'sr_deal.code as sr_deal_code',
                'grn_deal.code as grn_deal_code'
            )
                ->leftJoin('sys_purchase_grn as grn', 'grn.id', '=', 'sys_purchase_grn_license_key.grn_id')
                ->leftJoin('sys_chartofaccounts as vendor', 'vendor.id', '=', 'grn.vendors')
                ->leftJoin('sys_item_opening_stock as ops', function ($join) {
                    $join->on('ops.id', '=', 'sys_purchase_grn_license_key.opening_stock_id')
                        ->where('sys_purchase_grn_license_key.type', 2);
                })
                ->leftJoin('sys_stock_in as stkin', function ($join) {
                    $join->on('stkin.id', '=', 'sys_purchase_grn_license_key.opening_stock_id')
                        ->where('sys_purchase_grn_license_key.type', 3);
                })
                ->leftJoin('sys_sales_return as sr', 'sr.id', '=', 'sys_purchase_grn_license_key.sales_return_id')
                ->leftJoin('sys_chartofaccounts as customer', 'customer.id', '=', 'sr.customer')
                ->leftJoin('sys_crm_deals as sr_deal', 'sr_deal.id', '=', 'sr.deal_id')
                ->leftJoin('sys_crm_deals as grn_deal', 'grn_deal.id', '=', 'grn.deal_id')
                ->where('sys_purchase_grn_license_key.item_id', $itemId)
                ->where('sys_purchase_grn_license_key.company_id', $companyId);

            if ($outId > 0) {
                $ret = (clone $base)->where(function ($q) use ($outId, $cartId) {
                    $q->where(function ($onOut) use ($outId) {
                        $onOut->where('sys_purchase_grn_license_key.status', 2)
                            ->where('sys_purchase_grn_license_key.stock_out_id', $outId);
                    })->orWhere(function ($available) use ($cartId) {
                        $available->where('sys_purchase_grn_license_key.status', 1)
                            ->where(function ($pool) use ($cartId) {
                                $pool->where(function ($dnFree) {
                                    $dnFree->where('sys_purchase_grn_license_key.dn_id', 0)
                                        ->orWhereNull('sys_purchase_grn_license_key.dn_id');
                                })
                                    ->where(function ($prFree) {
                                        $prFree->where('sys_purchase_grn_license_key.purchase_return_id', 0)
                                            ->orWhereNull('sys_purchase_grn_license_key.purchase_return_id');
                                    })
                                    ->whereRaw('COALESCE(sys_purchase_grn_license_key.stock_out_id,0) = 0');
                            })
                            ->orWhere(function ($draft) use ($cartId) {
                                $draft->where('sys_purchase_grn_license_key.stock_out_id', -1)
                                    ->where('sys_purchase_grn_license_key.cart_id', $cartId);
                            });
                    });
                })
                    ->orderByRaw('CASE WHEN sys_purchase_grn_license_key.status = 2 THEN 0 ELSE 1 END')
                    ->orderBy('sys_purchase_grn_license_key.exp_date', 'asc')
                    ->orderByRaw("COALESCE(grn.grn_date, '9999-12-31') asc")
                    ->orderBy('sys_purchase_grn_license_key.id', 'asc')
                    ->get();
            } else {
                $ret = $base->where('sys_purchase_grn_license_key.status', 1)
                    ->where(function ($q) use ($cartId) {
                        $q->where(function ($pool) use ($cartId) {
                            $pool->where(function ($dnFree) {
                                $dnFree->where('sys_purchase_grn_license_key.dn_id', 0)
                                    ->orWhereNull('sys_purchase_grn_license_key.dn_id');
                            })
                                ->where(function ($prFree) {
                                    $prFree->where('sys_purchase_grn_license_key.purchase_return_id', 0)
                                        ->orWhereNull('sys_purchase_grn_license_key.purchase_return_id');
                                })
                                ->whereRaw('COALESCE(sys_purchase_grn_license_key.stock_out_id,0) = 0');
                        })->orWhere(function ($draft) use ($cartId) {
                            $draft->where('sys_purchase_grn_license_key.stock_out_id', -1)
                                ->where('sys_purchase_grn_license_key.cart_id', $cartId);
                        });
                    })
                    ->orderBy('sys_purchase_grn_license_key.exp_date', 'asc')
                    ->orderByRaw("COALESCE(grn.grn_date, '9999-12-31') asc")
                    ->orderBy('sys_purchase_grn_license_key.id', 'asc')
                    ->get();
            }

            return json_encode(array('data' => count($ret) > 0 ? $ret : []));
        } catch (\Exception $e) {
            return json_encode(array('data' => 'ERROR'));
        }
    }

    function stock_out_update_grn_license_key(Request $request)
    {
        try {
            if (!$this->licenseKeyHasColumn('stock_out_id')) {
                return json_encode(array('data' => [], 'error' => 'stock_out_id column is missing in sys_purchase_grn_license_key.'));
            }
            $qtyLimit = max(0, intval($request->qty_limit));
            $itemId = $request->item_id;
            $companyId = session('logged_session_data.company_id');
            $cartId = session('logged_session_data.cart_id');

            $stagingIds = collect(explode(',', (string) $request->id))
                ->map(function ($id) {
                    return intval(trim($id));
                })
                ->filter(function ($id) {
                    return $id > 0;
                })
                ->unique()
                ->values()
                ->all();

            $outEditId = max(0, intval($request->stock_out_id));
            $keepOutKeyIds = collect(explode(',', (string) $request->keep_stock_out_key_ids))
                ->map(function ($id) {
                    return intval(trim($id));
                })
                ->filter(function ($id) {
                    return $id > 0;
                })
                ->unique()
                ->values()
                ->all();

            $totalKeysSelected = count($stagingIds) + count($keepOutKeyIds);
            if ($qtyLimit > 0 && $totalKeysSelected > $qtyLimit) {
                return json_encode(array('error' => 'Only '.$qtyLimit.' license keys can be selected for this item quantity.'));
            }

            if ($outEditId > 0) {
                $validatedKeep = SysPurchaseGrnLicenseKey::whereIn('id', $keepOutKeyIds)
                    ->where('item_id', $itemId)
                    ->where('stock_out_id', $outEditId)
                    ->where('status', 2)
                    ->where('company_id', $companyId)
                    ->pluck('id')
                    ->all();

                $assignedOnOut = SysPurchaseGrnLicenseKey::where('item_id', $itemId)
                    ->where('stock_out_id', $outEditId)
                    ->where('status', 2)
                    ->where('company_id', $companyId)
                    ->pluck('id')
                    ->all();

                $toRelease = array_diff($assignedOnOut, $validatedKeep);
                foreach ($toRelease as $releaseId) {
                    SysPurchaseGrnLicenseKey::where('id', $releaseId)->update([
                        'status' => 1,
                        'stock_out_id' => 0,
                        'cart_id' => '',
                        'updated_by' => Auth::user()->id,
                        'updated_at' => Carbon::now('+04:00'),
                    ]);
                    DB::table('sys_purchase_grn_license_key_trn')
                        ->where('type', 6)
                        ->where('trn_id', $outEditId)
                        ->where('key_id', $releaseId)
                        ->delete();
                }
            }

            if (count($stagingIds) === 0) {
                SysPurchaseGrnLicenseKey::where([
                    'cart_id' => $cartId,
                    'stock_out_id' => '-1',
                    'item_id' => $itemId,
                ])->update([
                    'stock_out_id' => 0,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]);

                return json_encode(array('data' => []));
            }

            $dt = SysPurchaseGrnLicenseKey::whereIn('id', $stagingIds)
                ->where('item_id', $itemId)
                ->where('status', 1)
                ->where('company_id', $companyId)
                ->where(function ($q) use ($cartId) {
                    $q->where(function ($pool) {
                        $pool->where(function ($dnFree) {
                            $dnFree->where('dn_id', 0)->orWhereNull('dn_id');
                        })
                            ->where(function ($prFree) {
                                $prFree->where('purchase_return_id', 0)->orWhereNull('purchase_return_id');
                            })
                            ->whereRaw('COALESCE(stock_out_id,0) = 0');
                    })->orWhere(function ($draft) use ($cartId) {
                        $draft->where('stock_out_id', -1)
                            ->where('cart_id', $cartId);
                    });
                })
                ->orderBy('exp_date', 'asc')
                ->get();

            if (count($dt) > 0) {
                SysPurchaseGrnLicenseKey::where([
                    'cart_id' => $cartId,
                    'stock_out_id' => '-1',
                    'item_id' => $itemId,
                ])->update([
                    'stock_out_id' => 0,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]);
                foreach ($dt as $d) {
                    SysPurchaseGrnLicenseKey::where('id', $d->id)->update([
                        'cart_id' => $cartId,
                        'stock_out_id' => '-1',
                        'updated_by' => Auth::user()->id,
                        'updated_at' => Carbon::now('+04:00'),
                    ]);
                }
            }

            return json_encode(array('data' => []));
        } catch (\Exception $e) {
            return json_encode(array('data' => 'ERROR'));
        }
    }

}
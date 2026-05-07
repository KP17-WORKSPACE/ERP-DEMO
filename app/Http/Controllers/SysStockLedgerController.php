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
use App\SysCurrencySettings;
use App\SysHelper;
use App\SysItemOpeningStock;
use App\SysItemStock;
use App\SysPaymentTerms;
use App\SysShipping;
use App\SysSupplierType;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;

class SysStockLedgerController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$id=null)
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
            if($_POST){
            
                $from_date = SysHelper::normalizeToYmd($request->from_date);
                $to_date = SysHelper::normalizeToYmd($request->to_date);
                $str_partno = $request->part_number;
                $part_number = explode(',',$request->part_number);
                $opb_date = Carbon::parse($from_date)->subDay()->format('Y-m-d');
                
            if(count($part_number)>0){
                foreach($part_number as $part_no){
                        $partnolist[] = $part_no;
                        $stocklist[] = SysItemStock::select('sys_item_stock.doc_number','sys_item_stock.doc_date','sys_item_stock.refno','sys_item_stock.account_id','sys_item_stock.partno','sys_item_stock.description','sys_item_stock.qty_in','sys_item_stock.price_in','sys_item_stock.qty_out','sys_item_stock.price_out','sys_item_stock.deal_id','sys_item_stock.slno','sys_item_stock.item_id','sm_items.part_number','grn.ref_company_id as grn_reference','dln.supplier_name as dln_reference','srt.supplier_name as srt_reference','prt.ref_company_id as prt_reference')
                        ->join('sm_items','sm_items.id','sys_item_stock.partno')



                        ->leftjoin('sys_purchase_grn as grn', DB::raw("grn.doc_number COLLATE utf8mb4_unicode_ci"), DB::raw("sys_item_stock.doc_number COLLATE utf8mb4_unicode_ci"))
                        ->leftjoin('sys_delivery_note as dln', DB::raw("dln.doc_number COLLATE utf8mb4_unicode_ci"), DB::raw("sys_item_stock.doc_number COLLATE utf8mb4_unicode_ci"))
                        ->leftjoin('sys_sales_return as srt', DB::raw("srt.doc_number"), DB::raw("sys_item_stock.doc_number"))
                        ->leftjoin('sys_purchase_return as prt', DB::raw("prt.doc_number COLLATE utf8mb4_unicode_ci"), DB::raw("sys_item_stock.doc_number COLLATE utf8mb4_unicode_ci"))


                        ->whereRaw("DATE_FORMAT(sys_item_stock.doc_date, '%Y-%m-%d') >= '".$from_date."' and DATE_FORMAT(sys_item_stock.doc_date, '%Y-%m-%d') <= '".$to_date."'")
                        ->where('sm_items.part_number',$part_no)->where('sys_item_stock.status',1)->where('sm_items.status',1)
                        ->wherein('sys_item_stock.company_id',$company_id)
                        ->orderby('sys_item_stock.doc_date','asc')
                        ->orderby('sys_item_stock.slno','asc')
                        ->orderby('sys_item_stock.id','asc')
                        ->get();

                    }
                }
            } else {
                if($id != ""){
                    $partnolist[] = $id;
                        $stocklist[] = SysItemStock::select('sys_item_stock.doc_number','sys_item_stock.doc_date','sys_item_stock.refno','sys_item_stock.account_id','sys_item_stock.partno','sys_item_stock.description','sys_item_stock.qty_in','sys_item_stock.price_in','sys_item_stock.qty_out','sys_item_stock.price_out','sys_item_stock.deal_id','sys_item_stock.slno','sys_item_stock.item_id','sm_items.part_number','grn.ref_company_id as grn_reference','dln.supplier_name as dln_reference','srt.supplier_name as srt_reference','prt.ref_company_id as prt_reference')
                        ->join('sm_items','sm_items.id','sys_item_stock.partno')
                        
                        ->leftjoin('sys_purchase_grn as grn', DB::raw("grn.doc_number COLLATE utf8mb4_unicode_ci"), DB::raw("sys_item_stock.doc_number COLLATE utf8mb4_unicode_ci"))
                        ->leftjoin('sys_delivery_note as dln', DB::raw("dln.doc_number COLLATE utf8mb4_unicode_ci"), DB::raw("sys_item_stock.doc_number COLLATE utf8mb4_unicode_ci"))
                        ->leftjoin('sys_sales_return as srt', DB::raw("srt.doc_number"), DB::raw("sys_item_stock.doc_number"))
                        ->leftjoin('sys_purchase_return as prt', DB::raw("prt.doc_number COLLATE utf8mb4_unicode_ci"), DB::raw("sys_item_stock.doc_number COLLATE utf8mb4_unicode_ci"))

                        ->whereRaw("DATE_FORMAT(sys_item_stock.doc_date, '%Y-%m-%d') >= '".$from_date."' and DATE_FORMAT(sys_item_stock.doc_date, '%Y-%m-%d') <= '".$to_date."'")
                        ->where('sm_items.part_number',$id)->where('sys_item_stock.status',1)->where('sm_items.status',1)
                        ->wherein('sys_item_stock.company_id',$company_id)
                        ->orderby('sys_item_stock.doc_date','asc')
                        ->orderby('sys_item_stock.slno','asc')
                        ->orderby('sys_item_stock.id','asc')
                        ->get();
                        $part_number = [$id];
                }
                
            }

            if (!empty($stocklist)) {
                $this->appendCfcToIncomingRate($stocklist, $company_id, $from_date, $to_date);
                $this->normalizeStockLedgerSerialNumbers($stocklist);
            }

            //return SysHelper::get_stock_ledger_opening_stock('R4W02A',$opb_date,$company_id);

            $items = SysHelper::get_product_list($company_id);

            return view('backEnd.inventory.StockLedger', compact('stocklist','partnolist','from_date','to_date','part_number','items','str_partno','opb_date','company_id'));
        }catch (\Exception $e) {
           return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }
    public function index_exe(Request $request,$id=null)
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
            $descriptions = [];
    
            $str_partno = $id;
            if($_POST){
                $from_date =Carbon::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');
                $to_date = Carbon::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');
                $str_partno = $request->part_number;
                $part_number = explode(',',$request->part_number);
                $opb_date = Carbon::parse($from_date)->subDay()->format('Y-m-d');
                
            if(count($part_number)>0){
                foreach($part_number as $part_no){

                    $descriptions[] = DB::table('sm_items')
                        ->where('part_number', $part_no)
                        ->orderByDesc('id')
                        ->value('description');


                        

                        $partnolist[] = $part_no;
                        
                        $stocklist[] = SysItemStock::select('sm_items.id as stockid','sys_item_stock.doc_number','sys_item_stock.doc_date','sys_item_stock.refno','sys_item_stock.account_id','sys_item_stock.partno','sys_item_stock.description','sys_item_stock.qty_in','sys_item_stock.price_in','sys_item_stock.qty_out','sys_item_stock.price_out','sys_item_stock.deal_id','sys_item_stock.slno','sys_item_stock.item_id','sm_items.part_number','grn.ref_company_id as grn_reference','dln.supplier_name as dln_reference','srt.supplier_name as srt_reference','prt.reference as prt_reference')
                        ->join('sm_items','sm_items.id','sys_item_stock.partno')



                        ->leftjoin('sys_purchase_grn as grn', DB::raw("grn.doc_number COLLATE utf8mb4_unicode_ci"), DB::raw("sys_item_stock.doc_number COLLATE utf8mb4_unicode_ci"))
                        ->leftjoin('sys_delivery_note as dln', DB::raw("dln.doc_number COLLATE utf8mb4_unicode_ci"), DB::raw("sys_item_stock.doc_number COLLATE utf8mb4_unicode_ci"))
                        ->leftjoin('sys_sales_return as srt', DB::raw("srt.doc_number"), DB::raw("sys_item_stock.doc_number"))
                        ->leftjoin('sys_purchase_return as prt', DB::raw("prt.doc_number COLLATE utf8mb4_unicode_ci"), DB::raw("sys_item_stock.doc_number COLLATE utf8mb4_unicode_ci"))


                        ->whereRaw("DATE_FORMAT(sys_item_stock.doc_date, '%Y-%m-%d') >= '".$from_date."' and DATE_FORMAT(sys_item_stock.doc_date, '%Y-%m-%d') <= '".$to_date."'")
                        ->where('sm_items.part_number',$part_no)->where('sys_item_stock.status',1)->where('sm_items.status',1)
                        ->wherein('sys_item_stock.company_id',$company_id)
                        ->orderby('sys_item_stock.doc_date','asc')
                        ->orderby('sys_item_stock.slno','asc')
                        ->orderby('sys_item_stock.id','asc')
                        ->get();

                        

                    }
                }
            } else {
                if($id != ""){
                    $partnolist[] = $id;

                      $descriptions[] = DB::table('sm_items')
                        ->where('part_number', $id)
                        ->orderByDesc('id')
                        ->value('description');
                    
                        $stocklist[] = SysItemStock::select('sm_items.id as stockid','sys_item_stock.doc_number','sys_item_stock.doc_date','sys_item_stock.refno','sys_item_stock.account_id','sys_item_stock.partno','sys_item_stock.description','sys_item_stock.qty_in','sys_item_stock.price_in','sys_item_stock.qty_out','sys_item_stock.price_out','sys_item_stock.deal_id','sys_item_stock.slno','sys_item_stock.item_id','sm_items.part_number','grn.ref_company_id as grn_reference','dln.supplier_name as dln_reference','srt.supplier_name as srt_reference','prt.ref_company_id as prt_reference')
                        ->join('sm_items','sm_items.id','sys_item_stock.partno')
                        
                        ->leftjoin('sys_purchase_grn as grn', DB::raw("grn.doc_number COLLATE utf8mb4_unicode_ci"), DB::raw("sys_item_stock.doc_number COLLATE utf8mb4_unicode_ci"))
                        ->leftjoin('sys_delivery_note as dln', DB::raw("dln.doc_number COLLATE utf8mb4_unicode_ci"), DB::raw("sys_item_stock.doc_number COLLATE utf8mb4_unicode_ci"))
                        ->leftjoin('sys_sales_return as srt', DB::raw("srt.doc_number"), DB::raw("sys_item_stock.doc_number"))
                        ->leftjoin('sys_purchase_return as prt', DB::raw("prt.doc_number COLLATE utf8mb4_unicode_ci"), DB::raw("sys_item_stock.doc_number COLLATE utf8mb4_unicode_ci"))

                        ->whereRaw("DATE_FORMAT(sys_item_stock.doc_date, '%Y-%m-%d') >= '".$from_date."' and DATE_FORMAT(sys_item_stock.doc_date, '%Y-%m-%d') <= '".$to_date."'")
                        ->where('sm_items.part_number',$id)->where('sys_item_stock.status',1)->where('sm_items.status',1)
                        ->wherein('sys_item_stock.company_id',$company_id)
                        ->orderby('sys_item_stock.doc_date','asc')
                        ->orderby('sys_item_stock.slno','asc')
                        ->orderby('sys_item_stock.id','asc')
                        ->get();
                        $part_number = [$id];
                }
                
            }

            if (!empty($stocklist)) {
                $this->appendCfcToIncomingRate($stocklist, $company_id, $from_date, $to_date);
                $this->normalizeStockLedgerSerialNumbers($stocklist);
            }

      

            //return SysHelper::get_stock_ledger_opening_stock('R4W02A',$opb_date,$company_id);

            $items = SysHelper::get_product_list($company_id);

            return view('backEnd.inventory.StockLedger', compact('stocklist','partnolist','from_date','to_date','part_number','items','str_partno','opb_date','company_id','descriptions'));
        }catch (\Exception $e) {
           return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }

    /**
     * Recalculate GRN/PI incoming rate from landed line components.
     */
    protected function appendCfcToIncomingRate(array &$stocklist, $companyIds, $fromDate, $toDate)
    {
        $companyIds = is_array($companyIds) ? $companyIds : [$companyIds];
        $companyIds = array_values(array_filter($companyIds, function ($x) {
            return $x !== null && $x !== '';
        }));
        if (empty($companyIds)) {
            return;
        }

        $grnPartRows = DB::table('sys_purchase_grn_items as gi')
            ->join('sys_purchase_grn as grn', 'grn.id', '=', 'gi.grn_id')
            ->select(
                'grn.doc_number',
                'gi.id as line_item_id',
                'gi.part_no as partno',
                'gi.part_number as part_number',
                DB::raw('SUM(IFNULL(gi.qty,0)) as qty'),
                DB::raw('SUM(IFNULL(gi.value, IFNULL(gi.unitprice,0) * IFNULL(gi.qty,0))) as line_value'),
                DB::raw('SUM(IFNULL(gi.discount,0)) as discount'),
                DB::raw('SUM(IFNULL(gi.fright,0)) as fright'),
                DB::raw('SUM(IFNULL(gi.customcharges,0)) as customcharges')
            )
            ->where('gi.status', 1)
            ->where('grn.status', 1)
            ->groupBy('grn.doc_number', 'gi.id', 'gi.part_no', 'gi.part_number')
            ->get();

        $piPartRows = DB::table('sys_purchase_invoice_items as pii')
            ->join('sys_purchase_invoice as pi', 'pi.id', '=', 'pii.pi_id')
            ->select(
                'pi.doc_number',
                'pii.id as line_item_id',
                'pii.part_number as partno',
                'item.part_number as part_number',
                DB::raw('SUM(IFNULL(pii.qty,0)) as qty'),
                DB::raw('SUM(IFNULL(pii.value, IFNULL(pii.unitprice,0) * IFNULL(pii.qty,0))) as line_value'),
                DB::raw('SUM(IFNULL(pii.discount,0)) as discount'),
                DB::raw('SUM(IFNULL(pii.fright,0)) as fright'),
                DB::raw('SUM(IFNULL(pii.customcharges,0)) as customcharges')
            )
            ->leftJoin('sm_items as item', 'item.id', '=', 'pii.part_number')
            ->where('pii.status', 1)
            ->where('pi.status', 1)
            ->groupBy('pi.doc_number', 'pii.id', 'pii.part_number', 'item.part_number')
            ->get();

        $grnPartMap = [];
        foreach ($grnPartRows as $r) {
            $grnPartMap[$r->doc_number . '|li:' . (int) $r->line_item_id] = $r;
            $grnPartMap[$r->doc_number . '|' . (int) $r->partno] = $r;
            $pn = trim((string) ($r->part_number ?? ''));
            if ($pn !== '') {
                $grnPartMap[$r->doc_number . '|pn:' . $pn] = $r;
            }
        }

        $piPartMap = [];
        foreach ($piPartRows as $r) {
            $piPartMap[$r->doc_number . '|li:' . (int) $r->line_item_id] = $r;
            $piPartMap[$r->doc_number . '|' . (int) $r->partno] = $r;
            $pn = trim((string) ($r->part_number ?? ''));
            if ($pn !== '') {
                $piPartMap[$r->doc_number . '|pn:' . $pn] = $r;
            }
        }

        $siPartRows = DB::table('sys_sales_invoice_items as sii')
            ->join('sys_sales_invoice as si', 'si.id', '=', 'sii.si_id')
            ->leftJoin('sm_items as item', 'item.id', '=', 'sii.part_number')
            ->select(
                'si.doc_number',
                'sii.id as line_item_id',
                'sii.part_number as partno',
                'item.part_number as part_number',
                DB::raw('SUM(IFNULL(sii.qty,0)) as qty'),
                DB::raw('SUM(IFNULL(sii.value, IFNULL(sii.unitprice,0) * IFNULL(sii.qty,0))) as line_value'),
                DB::raw('SUM(IFNULL(sii.discount,0)) as discount')
            )
            ->where('sii.status', 1)
            ->where('si.status', 1)
            ->groupBy('si.doc_number', 'sii.id', 'sii.part_number', 'item.part_number')
            ->get();

        $dnPartRows = DB::table('sys_delivery_note_items as dnl')
            ->join('sys_delivery_note as dn', 'dn.id', '=', 'dnl.dn_id')
            ->leftJoin('sm_items as item', 'item.id', '=', 'dnl.part_number')
            ->select(
                'dn.doc_number',
                'dn.invoice_no as invoice_no',
                'dnl.id as line_item_id',
                'dnl.part_number as partno',
                'item.part_number as part_number',
                DB::raw('SUM(IFNULL(dnl.qty,0)) as qty'),
                DB::raw('SUM(IFNULL(dnl.value, IFNULL(dnl.unitprice,0) * IFNULL(dnl.qty,0))) as line_value'),
                DB::raw('SUM(IFNULL(dnl.discount,0)) as discount')
            )
            ->where('dnl.status', 1)
            ->where('dn.status', 1)
            ->groupBy('dn.doc_number', 'dn.invoice_no', 'dnl.id', 'dnl.part_number', 'item.part_number')
            ->get();

        $salesCfcByDoc = DB::table('sys_sales_invoice_cf_charges')
            ->select('si_doc_number', DB::raw('SUM(cfc_amount) as total_cfc'))
            ->where('status', 1)
            ->groupBy('si_doc_number')
            ->pluck('total_cfc', 'si_doc_number');

        $siDocValue = DB::table('sys_sales_invoice_items as sii')
            ->join('sys_sales_invoice as si', 'si.id', '=', 'sii.si_id')
            ->select(
                'si.doc_number',
                DB::raw('SUM(IFNULL(sii.value, IFNULL(sii.unitprice,0) * IFNULL(sii.qty,0))) as doc_value')
            )
            ->where('sii.status', 1)
            ->where('si.status', 1)
            ->groupBy('si.doc_number')
            ->pluck('doc_value', 'doc_number');

        $dnDocValue = DB::table('sys_delivery_note_items as dnl')
            ->join('sys_delivery_note as dn', 'dn.id', '=', 'dnl.dn_id')
            ->select(
                'dn.doc_number',
                DB::raw('SUM(IFNULL(dnl.value, IFNULL(dnl.unitprice,0) * IFNULL(dnl.qty,0))) as doc_value')
            )
            ->where('dnl.status', 1)
            ->where('dn.status', 1)
            ->groupBy('dn.doc_number')
            ->pluck('doc_value', 'doc_number');

        $siPartMap = [];
        foreach ($siPartRows as $r) {
            $siPartMap[$r->doc_number . '|li:' . (int) $r->line_item_id] = $r;
            $siPartMap[$r->doc_number . '|' . (int) $r->partno] = $r;
            $pn = trim((string) ($r->part_number ?? ''));
            if ($pn !== '') {
                $siPartMap[$r->doc_number . '|pn:' . $pn] = $r;
            }
        }

        $dnPartMap = [];
        foreach ($dnPartRows as $r) {
            $dnPartMap[$r->doc_number . '|li:' . (int) $r->line_item_id] = $r;
            $dnPartMap[$r->doc_number . '|' . (int) $r->partno] = $r;
            $pn = trim((string) ($r->part_number ?? ''));
            if ($pn !== '') {
                $dnPartMap[$r->doc_number . '|pn:' . $pn] = $r;
            }
        }

        foreach ($stocklist as &$rows) {
            foreach ($rows as &$row) {
                $docNo = trim((string) ($row->doc_number ?? ''));
                if ($docNo === '') {
                    continue;
                }

                $prefix = strtoupper(substr($docNo, 0, 2));
                $partId = (int) ($row->partno ?? 0);
                $partNumber = trim((string) ($row->part_number ?? ''));
                $lineItemId = (int) ($row->item_id ?? 0);
                if ($partId <= 0) {
                    if ($partNumber === '') {
                        continue;
                    }
                }

                $finalRate = null;
                if ($prefix === 'GR' && (float) ($row->qty_in ?? 0) > 0) {
                    $k = $docNo . '|li:' . $lineItemId;
                    if ($lineItemId <= 0 || !isset($grnPartMap[$k])) {
                        $k = $docNo . '|' . $partId;
                    }
                    if (!isset($grnPartMap[$k]) && $partNumber !== '') {
                        $k = $docNo . '|pn:' . $partNumber;
                    }
                    if (!isset($grnPartMap[$k])) {
                        continue;
                    }
                    $p = $grnPartMap[$k];
                    $qty = (float) ($p->qty ?? 0);
                    if ($qty <= 0) {
                        continue;
                    }
                    $lineValue = (float) ($p->line_value ?? 0);
                    $baseAfterDiscount = $lineValue - (float) ($p->discount ?? 0);
                    $lineExtras = (float) ($p->fright ?? 0) + (float) ($p->customcharges ?? 0);
                    $finalRate = ($baseAfterDiscount + $lineExtras) / $qty;
                } elseif ($prefix === 'PI' && (float) ($row->qty_in ?? 0) > 0) {
                    $k = $docNo . '|li:' . $lineItemId;
                    if ($lineItemId <= 0 || !isset($piPartMap[$k])) {
                        $k = $docNo . '|' . $partId;
                    }
                    if (!isset($piPartMap[$k]) && $partNumber !== '') {
                        $k = $docNo . '|pn:' . $partNumber;
                    }
                    if (!isset($piPartMap[$k])) {
                        continue;
                    }
                    $p = $piPartMap[$k];
                    $qty = (float) ($p->qty ?? 0);
                    if ($qty <= 0) {
                        continue;
                    }
                    $lineValue = (float) ($p->line_value ?? 0);
                    $baseAfterDiscount = $lineValue - (float) ($p->discount ?? 0);
                    $lineExtras = (float) ($p->fright ?? 0) + (float) ($p->customcharges ?? 0);
                    $finalRate = ($baseAfterDiscount + $lineExtras) / $qty;
                } elseif ($prefix === 'SI' && (float) ($row->qty_out ?? 0) > 0) {
                    $k = $docNo . '|li:' . $lineItemId;
                    if ($lineItemId <= 0 || !isset($siPartMap[$k])) {
                        $k = $docNo . '|' . $partId;
                    }
                    if (!isset($siPartMap[$k]) && $partNumber !== '') {
                        $k = $docNo . '|pn:' . $partNumber;
                    }
                    if (!isset($siPartMap[$k])) {
                        continue;
                    }
                    $p = $siPartMap[$k];
                    $qty = (float) ($p->qty ?? 0);
                    if ($qty <= 0) {
                        continue;
                    }
                    $lineValue = (float) ($p->line_value ?? 0);
                    $lineDiscount = (float) ($p->discount ?? 0);
                    $docValue = (float) ($siDocValue[$docNo] ?? 0);
                    $docCfc = (float) ($salesCfcByDoc[$docNo] ?? 0);
                    $allocatedCfc = $docValue > 0 ? (($lineValue / $docValue) * $docCfc) : 0.0;
                    $finalRate = ($lineValue - $lineDiscount - $allocatedCfc) / $qty;
                    $row->price_out = $finalRate >= 0 ? $finalRate : 0;
                    continue;
                } elseif ($prefix === 'DN' && (float) ($row->qty_out ?? 0) > 0) {
                    $k = $docNo . '|li:' . $lineItemId;
                    if ($lineItemId <= 0 || !isset($dnPartMap[$k])) {
                        $k = $docNo . '|' . $partId;
                    }
                    if (!isset($dnPartMap[$k]) && $partNumber !== '') {
                        $k = $docNo . '|pn:' . $partNumber;
                    }
                    if (!isset($dnPartMap[$k])) {
                        continue;
                    }
                    $p = $dnPartMap[$k];
                    $qty = (float) ($p->qty ?? 0);
                    if ($qty <= 0) {
                        continue;
                    }
                    $lineValue = (float) ($p->line_value ?? 0);
                    $lineDiscount = (float) ($p->discount ?? 0);
                    $docValue = (float) ($dnDocValue[$docNo] ?? 0);
                    $invoiceNo = trim((string) ($p->invoice_no ?? ''));
                    $docCfc = $invoiceNo !== '' ? (float) ($salesCfcByDoc[$invoiceNo] ?? 0) : 0.0;
                    $allocatedCfc = $docValue > 0 ? (($lineValue / $docValue) * $docCfc) : 0.0;
                    $finalRate = ($lineValue - $lineDiscount - $allocatedCfc) / $qty;
                    $row->price_out = $finalRate >= 0 ? $finalRate : 0;
                    continue;
                } else {
                    continue;
                }

                if ($finalRate !== null && $finalRate >= 0) {
                    $row->price_in = $finalRate;
                }
            }
        }
    }

    /**
     * Clean and standardize serial numbers for stock ledger rows.
     */
    protected function normalizeStockLedgerSerialNumbers(array &$stocklist)
    {
        foreach ($stocklist as &$rows) {
            foreach ($rows as &$row) {
                $raw = trim((string) ($row->slno ?? ''));
                if ($raw === '') {
                    $row->slno = '';
                    continue;
                }

                $serials = [];
                $decoded = null;
                if (substr($raw, 0, 1) === '[' && substr($raw, -1) === ']') {
                    $decoded = json_decode($raw, true);
                }

                if (is_array($decoded)) {
                    foreach ($decoded as $serial) {
                        $serial = trim((string) $serial);
                        if ($serial !== '') {
                            $serials[] = $serial;
                        }
                    }
                } else {
                    $raw = str_replace(["\r\n", "\r", "\n", ';', '|'], ',', $raw);
                    $parts = array_map('trim', explode(',', $raw));
                    foreach ($parts as $serial) {
                        if ($serial !== '') {
                            $serials[] = $serial;
                        }
                    }
                }

                if (empty($serials)) {
                    $row->slno = '';
                    continue;
                }

                $serials = array_values(array_unique($serials));
                $row->slno = implode(', ', $serials);
            }
        }
    }

    public function inventory_report(Request $request)
    {
       
        try{
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            
            $doc_no="";
            $deal_id="";
            $acc_id="";
            $sales_person="";
            $ageing="";

            $from_date="";
            $ctrl_ageing="";
            $ctrl_doc_number="";
            $ctrl_deal_id="";
            $ctrl_supplier="";
            $ctrl_sales_person="";

            $brand = SysBrand::select('id','title')->orderby('title','asc')->get();
            $category = DB::table('sm_item_categories')->select('id','category_name')->orderby('category_name','asc')->get();
            $sub_category = DB::table('sm_item_subcategories')->select('id','sub_category_name')->orderby('sub_category_name','asc')->get();
            $supplier_list = SysHelper::get_supplier_list($company_id);
            $sales_person_list = SysHelper::get_sales_persons();

            $to_date = date('Y-m-d');
            $stocklist = [];
            $stocklist_return = [];
            $r_part_number=""; $r_brand=""; $r_category=""; $r_sub_category=""; $r_qty="";
            if($_POST){
                $from_date = $request->from_date
                    ? Carbon::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d')
                    : Carbon::now()->format('Y-m-d');

                $to_date = $request->to_date
                    ? Carbon::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d')
                    : Carbon::now()->format('Y-m-d');
                
                $stocklist_query = DB::table('sys_item_stock as stock')
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
                ->wherein('stock.company_id',$company_id)->where('stock.status',1)
                ->where('stock.doc_number', 'not like', 'SRN%')->wherein('item.product_type',[1,2]);              
                
                

                if ($to_date != "" && $from_date == "") {
                    $stocklist_query->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') <= '" . $to_date . "'");
                }
                elseif ($to_date == "" && $from_date != "") {
                    $stocklist_query->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') >= '". $from_date ."'");
                }
                elseif ($request->search_from_date != "" && $request->search_to_date != "") {
                    $stocklist_query->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') >= '". $from_date ."'");
                    $stocklist_query->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') <= '". $to_date."'");
                }
                else {
                    $stocklist_query->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') <= '" . $to_date . "'");
                }

                $stocklist_query->wherein('stock.company_id',$company_id)->where('stock.status',1)
                ->where('stock.doc_number', 'not like', 'SRN%')
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
                if($request->ageing != ""){
                    $ctrl_ageing = $request->ageing;
                }
                if($request->doc_number != ""){
                    $ctrl_doc_number = $request->doc_number;
                    $doc_no = $request->doc_number;
                }
                if($request->deal_id != ""){
                    //$stocklist_query->where('stock.deal_id',$request->deal_id);
                    $ctrl_deal_id = $request->deal_id;
                    $deal_id = $request->deal_id;
                }
                if($request->supplier != ""){
                    //$stocklist_query->where('stock.account_id',$request->supplier);
                    $ctrl_supplier = $request->supplier;
                    $acc_id = $request->supplier;
                }
                if($request->sales_person != ""){
                    $ctrl_sales_person = $request->sales_person;
                    $sales_person = $request->sales_person;
                }
                if($request->ageing != ""){
                    $ctrl_ageing = $request->ageing;
                    $ageing = $request->ageing;
                }

                $stocklist = $stocklist_query->groupby('item.part_number','item.description','brand.title')->get(); 

                $stocklist_return = DB::table('sys_item_stock')->select(DB::raw('max(partno) as partno'),DB::raw('SUM(qty_in) as qty'))
                ->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') <= '" . $to_date . "'")->wherein('company_id',$company_id)->where('doc_number', 'like', 'SRN%')->where('status',1)
                ->groupby('partno')->get();
                
                $part_number = $stocklist->pluck('part_number');
                if(count($part_number)>0){
                    $stockledgerlist = SysHelper::get_inventory_report($part_number,$to_date,$company_id,$doc_no,$deal_id,$acc_id,$sales_person,$ageing);
                    }

            }
            else{
                
                $stocklist = DB::table('sys_item_stock as stock')
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
                ->wherein('stock.company_id',$company_id)->where('stock.status',1)
                ->where('stock.doc_number', 'not like', 'SRN%')
                ->wherein('item.product_type',[1,2])
                ->groupby('item.part_number','item.description','brand.title')
                ->get();            

                $stocklist_return = DB::table('sys_item_stock')->select(DB::raw('max(partno) as partno'),DB::raw('SUM(qty_in) as qty'))
                ->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') <= '" . $to_date . "'")->wherein('company_id',$company_id)->where('doc_number', 'like', 'SRN%')->where('status',1)
                ->groupby('partno')->get();                
            }
            
            if(Auth::user()->role_id==1){
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
            
            $company_list = DB::table('sys_company')->select('id','company_name')->wherenotin('id',[1])->orderby('sort_id','asc')->get();

            $part_number = $stocklist->pluck('part_number');

            $stockledgerlist = [];

            if(count($part_number)>0){
                $stockledgerlist = SysHelper::get_inventory_report($part_number,$to_date,$company_id,$doc_no,$deal_id,$acc_id,$sales_person,$ageing);
                //return $stockledgerlist;
                        //  $stockledgerlist = SysItemStock::select('sys_item_stock.doc_number','sys_item_stock.doc_date','sys_item_stock.refno','sys_item_stock.account_id','sys_item_stock.partno','sys_item_stock.description','sys_item_stock.qty_in','sys_item_stock.price_in','sys_item_stock.qty_out','sys_item_stock.price_out','sys_item_stock.deal_id','sys_item_stock.slno','sm_items.part_number','sm_staffs.full_name')
                        //  ->join('sm_items','sm_items.id','sys_item_stock.partno')
                        //  ->leftjoin('sys_delivery_note','sys_delivery_note.id','sys_item_stock.dln_id')
                        //  ->leftjoin('sm_staffs','sm_staffs.user_id','sys_delivery_note.salesman')
                        //  ->whereRaw("DATE_FORMAT(sys_item_stock.doc_date, '%Y-%m-%d') <= '".$to_date."'")
                        //  ->wherein('sm_items.part_number',$part_number)->where('sys_item_stock.status',1)
                        //  ->wherein('sys_item_stock.company_id',$company_id)
                        //  ->orderby('sys_item_stock.doc_date','asc')
                        //  ->get();
                }
                //return $stockledgerlist;

            
            return view('backEnd.inventory.InventoryReport', compact('stocklist','to_date','stocklist_return','brand','category','sub_category','r_part_number','r_brand','r_category','r_sub_category','r_qty','company_list','show_all','show_brand','stockledgerlist','supplier_list','sales_person_list','from_date', 'ctrl_ageing', 'ctrl_doc_number', 'ctrl_deal_id', 'ctrl_supplier', 'ctrl_sales_person'));
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }


    function fetch(Request $request)
    {
        try {
            $company_id = [session('logged_session_data.company_id')];
            if($request->get('query'))
            {
                $query = $request->get('query');
                /*$data = DB::table('sm_items')->where('part_number', 'LIKE', "%{$query}%")
                ->get();*/                
                $data = SmItem::select('id','part_number','description','coo','hscode','weight')->where('status',1)->where(
                    function ($query) use ($company_id) {
                        foreach ($company_id as $cid) {
                            $query->orWhereRaw("FIND_IN_SET($cid, company_id) > 0");
                        }
                    }
                )->where('part_number', 'LIKE', "%{$query}%")->orderby('part_number','ASC')->get();

                $output = '<ul class="form-control" style="list-style: none; height: auto; position: absolute; z-index: 999; line-height: 25px;">';
                foreach($data as $row)
                {
                    $output .= '<li><a href="#">'.$row->part_number.'</a></li>';
                }
                $output .= '</ul>';
                echo $output;
            }
        } catch (\Throwable $th) {
            //return $th;
        }
    }
    function fetch_name(Request $request)
    {
        try {
            $company_id = [session('logged_session_data.company_id')];
            if($request->get('query'))
            {
                $query = $request->get('query');
                //$data = DB::table('sm_items')->select('description')->where('part_number', 'LIKE', "%{$query}%")->orwhere('description', 'LIKE', "%{$query}%")->get();
                $data = SmItem::select('description')->where('status',1)->where(
                    function ($query) use ($company_id) {
                        foreach ($company_id as $cid) {
                            $query->orWhereRaw("FIND_IN_SET($cid, company_id) > 0");
                        }
                    }
                )->where('part_number', 'LIKE', "%{$query}%")->orwhere('description', 'LIKE', "%{$query}%")->orderby('part_number','ASC')->get();
                
                $output = '<ul class="form-control" style="list-style: none; height: auto; position: absolute; z-index: 999; line-height: 25px;">';
                foreach($data as $row)
                {
                    $output .= '<li><a href="#">'.$row->description.'</a></li>';
                }
                $output .= '</ul>';
                echo $output;
            }
        } catch (\Throwable $th) {
            //return $th;
        }
    }
    
    function fetch_deal_name(Request $request)
    {
        try {            
            if($request->get('query'))
            {
                $query = $request->get('query');
                $data = DB::table('sm_items')->select('description')->where('part_number', 'LIKE', "%{$query}%")->orwhere('description', 'LIKE', "%{$query}%")->get();
                
                $output = '<ul class="form-control" style="list-style: none; height: auto; position: absolute; z-index: 999; line-height: 25px;">';
                foreach($data as $row)
                {
                    $output .= '<li><a href="#">'.$row->description.'</a></li>';
                }
                $output .= '</ul>';
                echo $output;
            }
        } catch (\Throwable $th) {
            //return $th;
        }
    }
    function fetch_product_partnumber(Request $request)
    {
        try {
            $company_id = [session('logged_session_data.company_id')];
            if($request->get('query'))
            {
                $query = $request->get('query');
                $data = SmItem::select('part_number','description')->where('status',1)->where('part_number', 'LIKE', "%{$query}%")->orderby('part_number','ASC')->get();

                $output = '<ul class="form-control" style="list-style: none; height: auto; position: absolute; z-index: 999; line-height: 25px;">';
                foreach($data as $row)
                {
                    $output .= '<li><a href="#">'.$row->part_number.'</a></li>';
                }
                $output .= '</ul>';
                echo $output;
            }
        } catch (\Throwable $th) {
            //return $th;
        }
    }    
    function fetch_product_partnumber_deal(Request $request)
    {
        try {
            $company_id = [session('logged_session_data.company_id')];
            if($request->get('query'))
            {
                $query = $request->get('query');
                $data = SmItem::select('id','part_number','description')->where('status',1)->where('part_number', 'LIKE', "%{$query}%")->orwhere('description', 'LIKE', "%{$query}%")->orderby('part_number','ASC')->limit(20)->get();
                $ids="";
                $output = '<ul class="form-control" style="list-style: none; height: auto; position: absolute; z-index: 999; line-height: 25px;">';
                if(count($data)>0){
                    foreach($data as $row)
                    {
                        $ids = str_replace(' ', '', $row->part_number);
                        $ids = str_replace('-', '', $ids);
                        $ids = str_replace('&', '', $ids);
                        $ids = str_replace('/', '', $ids);
                        $ids = preg_replace('/[^A-Za-z0-9\-]/', '', $ids);

                        $output .= '<li><a href="#">'.$row->part_number.'</a><input type="hidden" id="id_'.$ids.'" value="'.$row->id.'" /><input type="hidden" id="descroption_'.$ids.'" value="'.$row->description.'" /></li>';
                    }
                }
                else {
                    $output .= '<li>No Data Found!</li>';
                }
                $output .= '</ul>';
                echo $output;
            }
        } catch (\Throwable $th) {
            //return $th;
        }
    }
    function fetch_product_partnumber_withcoma(Request $request)
    {
        try {
            $company_id = [session('logged_session_data.company_id')];
            if($request->get('query'))
            {
                $get_query = $request->get('query');

                if (str_contains($get_query, ',')) {
                    $str = explode(',',$get_query);
                    $i = count($str)-1;
                    $query = $str[$i];
                }
                else{
                    $query=$get_query;
                }

                $data = SmItem::select('id','part_number','description')->where('status',1)->where('part_number', 'LIKE', "%{$query}%")->orwhere('description', 'LIKE', "%{$query}%")->orderby('part_number','ASC')->limit(50)->get();
                $ids="";
                $output = '<ul class="form-control" style="list-style: none; height: auto; position: absolute; z-index: 999; line-height: 25px;">';
                foreach($data as $row)
                {
                    $output .= '<li><a href="#">'.$row->part_number.'</a></li>';
                }
                $output .= '</ul>';
                echo $output;
            }
        } catch (\Throwable $th) {
            //return $th;
        }
    }

    public function get_product_list_ajax(Request $request)
    {
        try {
            $company_id = [session('logged_session_data.company_id')];
            $search = $request->search_text;
            $items = SmItem::select('id','part_number','description','coo','hscode','weight','product_type')->where('status',1)
            ->where(function ($query) use ($search) {
                    $query->where('part_number', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                })->orderby('description','ASC')->limit(50)->get();

            return response()->json($items);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }




   public function inventory_report_brand(Request $request)
    {
        try{
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            
                $stocklist = [];
                $stocklist_return = [];
                $part_number = [];
                $stockledgerlist = [];

            $doc_no="";
            $deal_id="";
            $acc_id="";
            $sales_person="";
            $ageing="";

            $from_date = date('Y-01-01');
            $ctrl_ageing="";
            $ctrl_doc_number="";
            $ctrl_deal_id="";
            $ctrl_supplier="";
            $ctrl_sales_person="";
            $ctrl_part_number="";
            $ctrl_brand="";
            $ctrl_category="";
            $ctrl_sub_category="";
            $ctrl_list_type="none";
            $ctrl_company = session('logged_session_data.company_id');
            

            $brand = SysBrand::select('id','title')->orderby('title','asc')->get();
            if(session('logged_session_data.company_id')==1){
                $company = SysCompany::select('id','company_name')->orderby('sort_id','asc')->get();
            } else {
                $company = SysCompany::select('id','company_name')->where('id',session('logged_session_data.company_id'))->orderby('sort_id','asc')->get();
            }
            $category = DB::table('sm_item_categories')->select('id','category_name')->orderby('category_name','asc')->get();
            $sub_category = DB::table('sm_item_subcategories')->select('id','sub_category_name')->orderby('sub_category_name','asc')->get();
            $supplier_list = SysHelper::get_customer_list($company_id);
            $sales_person_list = SysHelper::get_staff_list();

            $to_date = date('Y-m-d');
            $stocklist = [];
            $stocklist_return = [];
            if ($request->isMethod('post') || $request->isMethod('get')) {
                if ($request->isMethod('post')) {
                    $to_date = SysHelper::normalizeToYmd($request->to_date);
                    if ($to_date == '') {
                        $to_date = date('Y-m-d');
                    }
                    $from_date = SysHelper::normalizeToYmd($request->from_date);
                    if ($from_date == '') {
                        $from_date = date('Y-m-d');
                    }
                } else {
                    if ($request->filled('from_date')) {
                        $fd = SysHelper::normalizeToYmd($request->input('from_date'));
                        if ($fd !== '') {
                            $from_date = $fd;
                        }
                    }
                    if ($request->filled('to_date')) {
                        $td = SysHelper::normalizeToYmd($request->input('to_date'));
                        if ($td !== '') {
                            $to_date = $td;
                        }
                    }
                }

                $stocklist_query = DB::table('sm_items as item')
                ->select(DB::raw('max(item.part_number) as part_number'),DB::raw('max(item.id) as partno'),DB::raw('max(item.description) as description')
                ,DB::raw('max(brand.title) as brand'),DB::raw('max(brand.id) as brandid')
                ,DB::raw('max(cat.category_name) as categoryname'),DB::raw('max(subcat.sub_category_name) as subcategoryname'))
                ->selectRaw('2 as type')
                ->join('sys_brand as brand','brand.id','item.brand')
                ->leftjoin('sm_item_categories as cat','cat.id','item.category_name')
                ->leftjoin('sm_item_subcategories as subcat','subcat.id','item.subcategory_name')
                ->where('item.status',1)
                ->wherein('item.product_type',[1,2]);
                
                if ($request->filled('part_number')) {
                    $partNumbers = array_filter(array_map('trim', explode(',', (string) $request->part_number)));
                    if (count($partNumbers) > 1) {
                        $stocklist_query->whereIn('item.part_number', $partNumbers);
                    } elseif (count($partNumbers) === 1) {
                        $stocklist_query->where('item.part_number', 'like', '%'.$partNumbers[0].'%');
                    }
                    $ctrl_part_number = $request->part_number;
                }
                if ($request->filled('company')) {
                    $ctrl_company = $request->company;
                }
                if ($request->filled('brand')) {
                    $ctrl_brand = $request->brand;
                }
                if ($request->filled('category')) {
                    $stocklist_query->where('item.category_name', $request->category);
                    $ctrl_category = $request->category;
                }
                if ($request->filled('sub_category')) {
                    $stocklist_query->where('item.subcategory_name', $request->sub_category);
                    $ctrl_sub_category = $request->sub_category;
                }
                if ($request->filled('supplier')) {
                    $ctrl_supplier = $request->supplier;
                }
                if ($request->filled('sales_person')) {
                    $ctrl_sales_person = $request->sales_person;
                }
                
                $ctrl_list_type = $request->input('list_type', $ctrl_list_type);

                $stocklist2 = $stocklist_query->groupby('item.part_number','item.description','brand.title')->get();
                
                $part_number = $stocklist2->pluck('part_number');
                //return $part_number;
                
                if (count($part_number) > 0) {
                    $siLines = self::sales_report_detail($ctrl_brand, $part_number, $from_date, $to_date, $ctrl_sales_person, $ctrl_supplier, $ctrl_company);
                    $srLines = self::sales_return_report_detail($ctrl_brand, $part_number, $from_date, $to_date, $ctrl_sales_person, $ctrl_supplier, $ctrl_company);
                    if (!is_iterable($siLines)) {
                        $siLines = collect();
                    } else {
                        $siLines = collect($siLines);
                    }
                    if (!is_iterable($srLines)) {
                        $srLines = collect();
                    } else {
                        $srLines = collect($srLines);
                    }
                    $stockledgerlist = $siLines->merge($srLines)->values();
                    $itemIdsWithMovement = $siLines->pluck('item_id')->merge($srLines->pluck('item_id'))->map(function ($id) {
                        return (int) $id;
                    })->unique()->filter();
                    if ($itemIdsWithMovement->isNotEmpty()) {
                        $stocklist = $stocklist2->whereIn('partno', $itemIdsWithMovement->all());
                    }
                }
            }

            $brand_report_totals = [];
            $grand_qty = 0;
            $grand_value = 0;
            $grand_discount = 0;
            $grand_taxableamount = 0;
            $grand_vatamount = 0;
            $grand_total_amount = 0;
            $grand_avg_rate = 0;
            $grand_qty_si = 0;
            $grand_qty_sr = 0;
            $grand_value_si = 0;
            $grand_value_sr = 0;
            $grand_discount_si = 0;
            $grand_discount_sr = 0;
            $grand_taxableamount_si = 0;
            $grand_taxableamount_sr = 0;
            $grand_vatamount_si = 0;
            $grand_vatamount_sr = 0;
            $grand_total_amount_si = 0;
            $grand_total_amount_sr = 0;
            $grand_avg_rate_si = 0;
            $grand_avg_rate_sr = 0;

            if (count($stocklist) > 0) {
                $siLines = self::sales_report_detail($ctrl_brand, collect($stocklist)->pluck('part_number'), $from_date, $to_date, $ctrl_sales_person, $ctrl_supplier, $ctrl_company);
                $srLines = self::sales_return_report_detail($ctrl_brand, collect($stocklist)->pluck('part_number'), $from_date, $to_date, $ctrl_sales_person, $ctrl_supplier, $ctrl_company);
                if (!is_iterable($siLines)) {
                    $siLines = collect();
                } else {
                    $siLines = collect($siLines);
                }
                if (!is_iterable($srLines)) {
                    $srLines = collect();
                } else {
                    $srLines = collect($srLines);
                }
                [$byPart, $grandSplit] = self::inventoryBrandMergeSiSrByItem($siLines, $srLines);
                foreach ($stocklist as $row) {
                    $pid = (int) $row->partno;
                    $t = $byPart[$pid] ?? self::inventoryBrandEmptyPartTotals();
                    $brand_report_totals[$row->partno] = [
                        'qty' => $t['qty'],
                        'avg_rate' => $t['avg_rate'],
                        'value' => $t['value'],
                        'discount' => $t['discount'],
                        'taxableamount' => $t['taxableamount'],
                        'vatamount' => $t['vatamount'],
                        'total_amount' => $t['total_amount'],
                    ];
                }
                $grand_qty_si = $grandSplit['qty_si'];
                $grand_qty_sr = $grandSplit['qty_sr'];
                $grand_qty = $grandSplit['qty'];
                $grand_value_si = $grandSplit['value_si'];
                $grand_value_sr = $grandSplit['value_sr'];
                $grand_value = $grandSplit['value'];
                $grand_discount_si = $grandSplit['discount_si'];
                $grand_discount_sr = $grandSplit['discount_sr'];
                $grand_discount = $grandSplit['discount'];
                $grand_taxableamount_si = $grandSplit['taxableamount_si'];
                $grand_taxableamount_sr = $grandSplit['taxableamount_sr'];
                $grand_taxableamount = $grandSplit['taxableamount'];
                $grand_vatamount_si = $grandSplit['vatamount_si'];
                $grand_vatamount_sr = $grandSplit['vatamount_sr'];
                $grand_vatamount = $grandSplit['vatamount'];
                $grand_total_amount_si = $grandSplit['taxableamount_si'] + $grandSplit['vatamount_si'];
                $grand_total_amount_sr = $grandSplit['taxableamount_sr'] + $grandSplit['vatamount_sr'];
                $grand_total_amount = $grandSplit['total_amount'];
                $grand_avg_rate_si = $grandSplit['avg_rate_si'];
                $grand_avg_rate_sr = $grandSplit['avg_rate_sr'];
                $grand_avg_rate = $grandSplit['avg_rate'];
            }
            
            if(Auth::user()->role_id==1){
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
            
            $company_list = DB::table('sys_company')->select('id','company_name')->wherenotin('id',[1])->orderby('sort_id','asc')->get();

  


            
            return view('backEnd.inventory.InventoryBrandReport', compact(
                'stocklist',
                'to_date',
                'brand',
                'category',
                'sub_category',
                'ctrl_part_number',
                'ctrl_brand',
                'ctrl_category',
                'ctrl_sub_category',
                'company_list',
                'show_all',
                'show_brand',
                'stockledgerlist',
                'supplier_list',
                'sales_person_list',
                'from_date',
                'ctrl_ageing',
                'ctrl_doc_number',
                'ctrl_deal_id',
                'ctrl_supplier',
                'ctrl_sales_person',
                'ctrl_list_type',
                'company',
                'ctrl_company',
                'brand_report_totals',
                'grand_qty',
                'grand_value',
                'grand_discount',
                'grand_taxableamount',
                'grand_vatamount',
                'grand_total_amount',
                'grand_avg_rate',
                'grand_qty_si',
                'grand_qty_sr',
                'grand_value_si',
                'grand_value_sr',
                'grand_discount_si',
                'grand_discount_sr',
                'grand_taxableamount_si',
                'grand_taxableamount_sr',
                'grand_vatamount_si',
                'grand_vatamount_sr',
                'grand_total_amount_si',
                'grand_total_amount_sr',
                'grand_avg_rate_si',
                'grand_avg_rate_sr'
            ));
        }catch (\Exception $e) {
           return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }

    public function inventory_report_brand_wise(Request $request)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $from_date = date('Y-01-01');
            $to_date = date('Y-m-d');
            $ctrl_supplier = '';
            $ctrl_sales_person = '';
            $ctrl_company = session('logged_session_data.company_id');

            if ($request->isMethod('post')) {
                $to_date = SysHelper::normalizeToYmd($request->to_date);
                if ($to_date == '') {
                    $to_date = date('Y-m-d');
                }
                $from_date = SysHelper::normalizeToYmd($request->from_date);
                if ($from_date == '') {
                    $from_date = date('Y-m-d');
                }
            } else {
                if ($request->filled('from_date')) {
                    $fd = SysHelper::normalizeToYmd($request->input('from_date'));
                    if ($fd !== '') {
                        $from_date = $fd;
                    }
                }
                if ($request->filled('to_date')) {
                    $td = SysHelper::normalizeToYmd($request->input('to_date'));
                    if ($td !== '') {
                        $to_date = $td;
                    }
                }
            }

            if ($request->filled('company')) {
                $ctrl_company = $request->company;
            }
            if ($request->filled('supplier')) {
                $ctrl_supplier = $request->supplier;
            }
            if ($request->filled('sales_person')) {
                $ctrl_sales_person = $request->sales_person;
            }

            $dateFrom = date('Y-m-d', strtotime($from_date));
            $dateTo = date('Y-m-d', strtotime($to_date));

            $brandRowsSi = DB::table('sys_sales_invoice_items as si')
                ->select(
                    'b.id as brand_id',
                    'b.title as brand',
                    DB::raw('SUM(si.qty) as qty'),
                    DB::raw('SUM(si.value) as value'),
                    DB::raw('SUM(si.discount) as discount'),
                    DB::raw('SUM(si.taxableamount) as taxableamount'),
                    DB::raw('SUM(si.vatamount) as vatamount'),
                    DB::raw('SUM(si.taxableamount + si.vatamount) as total_amount')
                )
                ->join('sys_sales_invoice as s', 's.id', '=', 'si.si_id')
                ->join('sm_items as i', 'i.id', '=', 'si.part_number')
                ->join('sys_brand as b', 'b.id', '=', 'i.brand')
                ->where('s.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(s.doc_date)'), [$dateFrom, $dateTo]);

            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $brandRowsSi->where('s.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $brandRowsSi->where('s.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $brandRowsSi->where('s.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $brandRowsSi->where('s.sales_man', Auth::user()->id);
            }

            $brandRowsSi = $brandRowsSi->groupBy('b.id', 'b.title')->get()->keyBy('brand_id');

            $brandRowsSr = DB::table('sys_sales_return_list as srl')
                ->select(
                    'b.id as brand_id',
                    'b.title as brand',
                    DB::raw('SUM(srl.qty) as qty'),
                    DB::raw('SUM(srl.value) as value'),
                    DB::raw('SUM(srl.discount) as discount'),
                    DB::raw('SUM(srl.taxableamount) as taxableamount'),
                    DB::raw('SUM(srl.vatamount) as vatamount'),
                    DB::raw('SUM(srl.taxableamount + srl.vatamount) as total_amount')
                )
                ->join('sys_sales_return as sr', 'sr.id', '=', 'srl.sr_id')
                ->join('sm_items as i', 'i.id', '=', 'srl.part_number')
                ->join('sys_brand as b', 'b.id', '=', 'i.brand')
                ->where('sr.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(sr.doc_date)'), [$dateFrom, $dateTo]);

            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $brandRowsSr->where('sr.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $brandRowsSr->where('sr.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $brandRowsSr->where('sr.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $brandRowsSr->where('sr.sales_man', Auth::user()->id);
            }

            $brandRowsSr = $brandRowsSr->groupBy('b.id', 'b.title')->get()->keyBy('brand_id');

            $siDocs = DB::table('sys_sales_invoice_items as si')
                ->select('b.id as brand_id', DB::raw('COUNT(DISTINCT s.id) as doc_cnt'))
                ->join('sys_sales_invoice as s', 's.id', '=', 'si.si_id')
                ->join('sm_items as i', 'i.id', '=', 'si.part_number')
                ->join('sys_brand as b', 'b.id', '=', 'i.brand')
                ->where('s.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(s.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $siDocs->where('s.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $siDocs->where('s.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $siDocs->where('s.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $siDocs->where('s.sales_man', Auth::user()->id);
            }
            $siDocs = $siDocs->groupBy('b.id')->get()->keyBy('brand_id');

            $srDocs = DB::table('sys_sales_return_list as srl')
                ->select('b.id as brand_id', DB::raw('COUNT(DISTINCT sr.id) as doc_cnt'))
                ->join('sys_sales_return as sr', 'sr.id', '=', 'srl.sr_id')
                ->join('sm_items as i', 'i.id', '=', 'srl.part_number')
                ->join('sys_brand as b', 'b.id', '=', 'i.brand')
                ->where('sr.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(sr.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $srDocs->where('sr.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $srDocs->where('sr.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $srDocs->where('sr.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $srDocs->where('sr.sales_man', Auth::user()->id);
            }
            $srDocs = $srDocs->groupBy('b.id')->get()->keyBy('brand_id');

            $grandSiDocQ = DB::table('sys_sales_invoice as s')
                ->join('sys_sales_invoice_items as si', 'si.si_id', '=', 's.id')
                ->join('sm_items as i', 'i.id', '=', 'si.part_number')
                ->where('s.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(s.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $grandSiDocQ->where('s.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $grandSiDocQ->where('s.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $grandSiDocQ->where('s.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $grandSiDocQ->where('s.sales_man', Auth::user()->id);
            }
            $grand_si_doc_count = (int) $grandSiDocQ->select(DB::raw('COUNT(DISTINCT s.id) as c'))->value('c');

            $grandSrDocQ = DB::table('sys_sales_return as sr')
                ->join('sys_sales_return_list as srl', 'srl.sr_id', '=', 'sr.id')
                ->join('sm_items as i', 'i.id', '=', 'srl.part_number')
                ->where('sr.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(sr.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $grandSrDocQ->where('sr.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $grandSrDocQ->where('sr.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $grandSrDocQ->where('sr.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $grandSrDocQ->where('sr.sales_man', Auth::user()->id);
            }
            $grand_sr_doc_count = (int) $grandSrDocQ->select(DB::raw('COUNT(DISTINCT sr.id) as c'))->value('c');

            $brandIds = $brandRowsSi->keys()->merge($brandRowsSr->keys())->unique()->sort()->values();
            $brand_report_rows = [];
            $grand_qty = 0;
            $grand_value = 0;
            $grand_discount = 0;
            $grand_taxableamount = 0;
            $grand_vatamount = 0;
            $grand_total_amount = 0;
            $grand_qty_si = 0;
            $grand_qty_sr = 0;
            $grand_value_si = 0;
            $grand_value_sr = 0;
            $grand_discount_si = 0;
            $grand_discount_sr = 0;
            $grand_taxable_si = 0;
            $grand_taxable_sr = 0;
            $grand_vat_si = 0;
            $grand_vat_sr = 0;
            foreach ($brandIds as $bid) {
                $siR = $brandRowsSi->get($bid);
                $srR = $brandRowsSr->get($bid);
                $qtySi = (float) (optional($siR)->qty ?? 0);
                $qtySr = (float) (optional($srR)->qty ?? 0);
                $valueSi = (float) (optional($siR)->value ?? 0);
                $valueSr = (float) (optional($srR)->value ?? 0);
                $discSi = (float) (optional($siR)->discount ?? 0);
                $discSr = (float) (optional($srR)->discount ?? 0);
                $taxSi = (float) (optional($siR)->taxableamount ?? 0);
                $taxSr = (float) (optional($srR)->taxableamount ?? 0);
                $vatSi = (float) (optional($siR)->vatamount ?? 0);
                $vatSr = (float) (optional($srR)->vatamount ?? 0);
                $qty = $qtySi - $qtySr;
                $value = $valueSi - $valueSr;
                $discount = $discSi - $discSr;
                $taxableamount = $taxSi - $taxSr;
                $vatamount = $vatSi - $vatSr;
                $total_amount = $taxableamount + $vatamount;
                $total_amount_si = $taxSi + $vatSi;
                $total_amount_sr = $taxSr + $vatSr;
                $avg_rate = $qty > 0 ? $value / $qty : 0;
                $siDoc = (int) (optional($siDocs->get($bid))->doc_cnt ?? 0);
                $srDoc = (int) (optional($srDocs->get($bid))->doc_cnt ?? 0);

                $brand_report_rows[] = (object) [
                    'brand_id' => (int) $bid,
                    'brand' => optional($siR)->brand ?? optional($srR)->brand,
                    'qty' => $qty,
                    'avg_rate' => $avg_rate,
                    'value' => $value,
                    'discount' => $discount,
                    'taxableamount' => $taxableamount,
                    'vatamount' => $vatamount,
                    'total_amount' => $total_amount,
                    'total_amount_si' => $total_amount_si,
                    'total_amount_sr' => $total_amount_sr,
                    'si_doc_count' => $siDoc,
                    'sr_doc_count' => $srDoc,
                ];

                $grand_qty += $qty;
                $grand_value += $value;
                $grand_discount += $discount;
                $grand_taxableamount += $taxableamount;
                $grand_vatamount += $vatamount;
                $grand_total_amount += $total_amount;
                $grand_qty_si += $qtySi;
                $grand_qty_sr += $qtySr;
                $grand_value_si += $valueSi;
                $grand_value_sr += $valueSr;
                $grand_discount_si += $discSi;
                $grand_discount_sr += $discSr;
                $grand_taxable_si += $taxSi;
                $grand_taxable_sr += $taxSr;
                $grand_vat_si += $vatSi;
                $grand_vat_sr += $vatSr;
            }
            usort($brand_report_rows, function ($a, $b) {
                return strcmp($a->brand, $b->brand);
            });
            $grand_avg_rate = $grand_qty > 0 ? $grand_value / $grand_qty : 0;
            $grand_avg_rate_si = $grand_qty_si > 0 ? $grand_value_si / $grand_qty_si : 0;
            $grand_avg_rate_sr = $grand_qty_sr > 0 ? $grand_value_sr / $grand_qty_sr : 0;
            $grand_total_amount_si = $grand_taxable_si + $grand_vat_si;
            $grand_total_amount_sr = $grand_taxable_sr + $grand_vat_sr;

            $supplier_list = SysHelper::get_customer_list($company_id);
            $sales_person_list = SysHelper::get_staff_list();
            if (session('logged_session_data.company_id') == 1) {
                $company = SysCompany::select('id', 'company_name')->orderBy('sort_id', 'asc')->get();
            } else {
                $company = SysCompany::select('id', 'company_name')
                    ->where('id', session('logged_session_data.company_id'))
                    ->orderBy('sort_id', 'asc')
                    ->get();
            }

            return view('backEnd.inventory.InventoryBrandWiseReport', compact(
                'brand_report_rows',
                'from_date',
                'to_date',
                'ctrl_supplier',
                'ctrl_sales_person',
                'ctrl_company',
                'supplier_list',
                'sales_person_list',
                'company',
                'grand_qty',
                'grand_avg_rate',
                'grand_value',
                'grand_discount',
                'grand_taxableamount',
                'grand_vatamount',
                'grand_total_amount',
                'grand_total_amount_si',
                'grand_total_amount_sr',
                'grand_qty_si',
                'grand_qty_sr',
                'grand_avg_rate_si',
                'grand_avg_rate_sr',
                'grand_value_si',
                'grand_value_sr',
                'grand_discount_si',
                'grand_discount_sr',
                'grand_taxable_si',
                'grand_taxable_sr',
                'grand_vat_si',
                'grand_vat_sr',
                'grand_si_doc_count',
                'grand_sr_doc_count'
            ));
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function inventory_report_category_wise(Request $request)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $from_date = date('Y-01-01');
            $to_date = date('Y-m-d');
            $ctrl_supplier = '';
            $ctrl_sales_person = '';
            $ctrl_company = session('logged_session_data.company_id');

            if ($request->isMethod('post')) {
                $to_date = SysHelper::normalizeToYmd($request->to_date);
                if ($to_date == '') {
                    $to_date = date('Y-m-d');
                }
                $from_date = SysHelper::normalizeToYmd($request->from_date);
                if ($from_date == '') {
                    $from_date = date('Y-m-d');
                }
            } else {
                if ($request->filled('from_date')) {
                    $fd = SysHelper::normalizeToYmd($request->input('from_date'));
                    if ($fd !== '') {
                        $from_date = $fd;
                    }
                }
                if ($request->filled('to_date')) {
                    $td = SysHelper::normalizeToYmd($request->input('to_date'));
                    if ($td !== '') {
                        $to_date = $td;
                    }
                }
            }

            if ($request->filled('company')) {
                $ctrl_company = $request->company;
            }
            if ($request->filled('supplier')) {
                $ctrl_supplier = $request->supplier;
            }
            if ($request->filled('sales_person')) {
                $ctrl_sales_person = $request->sales_person;
            }

            $dateFrom = date('Y-m-d', strtotime($from_date));
            $dateTo = date('Y-m-d', strtotime($to_date));

            $categoryRowsSi = DB::table('sys_sales_invoice_items as si')
                ->select(
                    'cat.id as category_id',
                    DB::raw('COALESCE(cat.category_name, "Uncategorized") as category_name'),
                    DB::raw('SUM(si.qty) as qty'),
                    DB::raw('SUM(si.value) as value'),
                    DB::raw('SUM(si.discount) as discount'),
                    DB::raw('SUM(si.taxableamount) as taxableamount'),
                    DB::raw('SUM(si.vatamount) as vatamount'),
                    DB::raw('SUM(si.taxableamount + si.vatamount) as total_amount')
                )
                ->join('sys_sales_invoice as s', 's.id', '=', 'si.si_id')
                ->join('sm_items as i', 'i.id', '=', 'si.part_number')
                ->leftJoin('sm_item_categories as cat', 'cat.id', '=', 'i.category_name')
                ->where('s.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(s.doc_date)'), [$dateFrom, $dateTo]);

            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $categoryRowsSi->where('s.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $categoryRowsSi->where('s.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $categoryRowsSi->where('s.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $categoryRowsSi->where('s.sales_man', Auth::user()->id);
            }

            $categoryRowsSi = $categoryRowsSi->groupBy('cat.id', 'cat.category_name')->get()->keyBy(function ($r) {
                return $r->category_id === null ? '_null_' : (string) $r->category_id;
            });

            $categoryRowsSr = DB::table('sys_sales_return_list as srl')
                ->select(
                    'cat.id as category_id',
                    DB::raw('COALESCE(cat.category_name, "Uncategorized") as category_name'),
                    DB::raw('SUM(srl.qty) as qty'),
                    DB::raw('SUM(srl.value) as value'),
                    DB::raw('SUM(srl.discount) as discount'),
                    DB::raw('SUM(srl.taxableamount) as taxableamount'),
                    DB::raw('SUM(srl.vatamount) as vatamount'),
                    DB::raw('SUM(srl.taxableamount + srl.vatamount) as total_amount')
                )
                ->join('sys_sales_return as sr', 'sr.id', '=', 'srl.sr_id')
                ->join('sm_items as i', 'i.id', '=', 'srl.part_number')
                ->leftJoin('sm_item_categories as cat', 'cat.id', '=', 'i.category_name')
                ->where('sr.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(sr.doc_date)'), [$dateFrom, $dateTo]);

            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $categoryRowsSr->where('sr.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $categoryRowsSr->where('sr.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $categoryRowsSr->where('sr.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $categoryRowsSr->where('sr.sales_man', Auth::user()->id);
            }

            $categoryRowsSr = $categoryRowsSr->groupBy('cat.id', 'cat.category_name')->get()->keyBy(function ($r) {
                return $r->category_id === null ? '_null_' : (string) $r->category_id;
            });

            $siDocs = DB::table('sys_sales_invoice_items as si')
                ->select('cat.id as category_id', DB::raw('COUNT(DISTINCT s.id) as doc_cnt'))
                ->join('sys_sales_invoice as s', 's.id', '=', 'si.si_id')
                ->join('sm_items as i', 'i.id', '=', 'si.part_number')
                ->leftJoin('sm_item_categories as cat', 'cat.id', '=', 'i.category_name')
                ->where('s.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(s.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $siDocs->where('s.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $siDocs->where('s.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $siDocs->where('s.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $siDocs->where('s.sales_man', Auth::user()->id);
            }
            $siDocs = $siDocs->groupBy('cat.id')->get()->keyBy(function ($r) {
                return $r->category_id === null ? '_null_' : (string) $r->category_id;
            });

            $srDocs = DB::table('sys_sales_return_list as srl')
                ->select('cat.id as category_id', DB::raw('COUNT(DISTINCT sr.id) as doc_cnt'))
                ->join('sys_sales_return as sr', 'sr.id', '=', 'srl.sr_id')
                ->join('sm_items as i', 'i.id', '=', 'srl.part_number')
                ->leftJoin('sm_item_categories as cat', 'cat.id', '=', 'i.category_name')
                ->where('sr.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(sr.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $srDocs->where('sr.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $srDocs->where('sr.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $srDocs->where('sr.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $srDocs->where('sr.sales_man', Auth::user()->id);
            }
            $srDocs = $srDocs->groupBy('cat.id')->get()->keyBy(function ($r) {
                return $r->category_id === null ? '_null_' : (string) $r->category_id;
            });

            $grandSiDocQ = DB::table('sys_sales_invoice as s')
                ->join('sys_sales_invoice_items as si', 'si.si_id', '=', 's.id')
                ->join('sm_items as i', 'i.id', '=', 'si.part_number')
                ->where('s.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(s.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $grandSiDocQ->where('s.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $grandSiDocQ->where('s.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $grandSiDocQ->where('s.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $grandSiDocQ->where('s.sales_man', Auth::user()->id);
            }
            $grand_si_doc_count = (int) $grandSiDocQ->select(DB::raw('COUNT(DISTINCT s.id) as c'))->value('c');

            $grandSrDocQ = DB::table('sys_sales_return as sr')
                ->join('sys_sales_return_list as srl', 'srl.sr_id', '=', 'sr.id')
                ->join('sm_items as i', 'i.id', '=', 'srl.part_number')
                ->where('sr.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(sr.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $grandSrDocQ->where('sr.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $grandSrDocQ->where('sr.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $grandSrDocQ->where('sr.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $grandSrDocQ->where('sr.sales_man', Auth::user()->id);
            }
            $grand_sr_doc_count = (int) $grandSrDocQ->select(DB::raw('COUNT(DISTINCT sr.id) as c'))->value('c');

            $catKeys = $categoryRowsSi->keys()->merge($categoryRowsSr->keys())->unique()->sort()->values();
            $category_report_rows = [];
            $grand_qty = 0;
            $grand_value = 0;
            $grand_discount = 0;
            $grand_taxableamount = 0;
            $grand_vatamount = 0;
            $grand_total_amount = 0;
            $grand_qty_si = 0;
            $grand_qty_sr = 0;
            $grand_value_si = 0;
            $grand_value_sr = 0;
            $grand_discount_si = 0;
            $grand_discount_sr = 0;
            $grand_taxable_si = 0;
            $grand_taxable_sr = 0;
            $grand_vat_si = 0;
            $grand_vat_sr = 0;
            foreach ($catKeys as $ck) {
                $siR = $categoryRowsSi->get($ck);
                $srR = $categoryRowsSr->get($ck);
                $qtySi = (float) (optional($siR)->qty ?? 0);
                $qtySr = (float) (optional($srR)->qty ?? 0);
                $valueSi = (float) (optional($siR)->value ?? 0);
                $valueSr = (float) (optional($srR)->value ?? 0);
                $discSi = (float) (optional($siR)->discount ?? 0);
                $discSr = (float) (optional($srR)->discount ?? 0);
                $taxSi = (float) (optional($siR)->taxableamount ?? 0);
                $taxSr = (float) (optional($srR)->taxableamount ?? 0);
                $vatSi = (float) (optional($siR)->vatamount ?? 0);
                $vatSr = (float) (optional($srR)->vatamount ?? 0);
                $qty = $qtySi - $qtySr;
                $value = $valueSi - $valueSr;
                $discount = $discSi - $discSr;
                $taxableamount = $taxSi - $taxSr;
                $vatamount = $vatSi - $vatSr;
                $total_amount = $taxableamount + $vatamount;
                $total_amount_si = $taxSi + $vatSi;
                $total_amount_sr = $taxSr + $vatSr;
                $avg_rate = $qty > 0 ? $value / $qty : 0;
                $cid = optional($siR)->category_id ?? optional($srR)->category_id;
                $cname = optional($siR)->category_name ?? optional($srR)->category_name;
                $siDoc = (int) (optional($siDocs->get($ck))->doc_cnt ?? 0);
                $srDoc = (int) (optional($srDocs->get($ck))->doc_cnt ?? 0);

                $category_report_rows[] = (object) [
                    'category_id' => $cid,
                    'category_name' => $cname,
                    'qty' => $qty,
                    'avg_rate' => $avg_rate,
                    'value' => $value,
                    'discount' => $discount,
                    'taxableamount' => $taxableamount,
                    'vatamount' => $vatamount,
                    'total_amount' => $total_amount,
                    'total_amount_si' => $total_amount_si,
                    'total_amount_sr' => $total_amount_sr,
                    'si_doc_count' => $siDoc,
                    'sr_doc_count' => $srDoc,
                ];

                $grand_qty += $qty;
                $grand_value += $value;
                $grand_discount += $discount;
                $grand_taxableamount += $taxableamount;
                $grand_vatamount += $vatamount;
                $grand_total_amount += $total_amount;
                $grand_qty_si += $qtySi;
                $grand_qty_sr += $qtySr;
                $grand_value_si += $valueSi;
                $grand_value_sr += $valueSr;
                $grand_discount_si += $discSi;
                $grand_discount_sr += $discSr;
                $grand_taxable_si += $taxSi;
                $grand_taxable_sr += $taxSr;
                $grand_vat_si += $vatSi;
                $grand_vat_sr += $vatSr;
            }
            usort($category_report_rows, function ($a, $b) {
                return strcmp($a->category_name, $b->category_name);
            });
            $grand_avg_rate = $grand_qty > 0 ? $grand_value / $grand_qty : 0;
            $grand_avg_rate_si = $grand_qty_si > 0 ? $grand_value_si / $grand_qty_si : 0;
            $grand_avg_rate_sr = $grand_qty_sr > 0 ? $grand_value_sr / $grand_qty_sr : 0;
            $grand_total_amount_si = $grand_taxable_si + $grand_vat_si;
            $grand_total_amount_sr = $grand_taxable_sr + $grand_vat_sr;

            $supplier_list = SysHelper::get_customer_list($company_id);
            $sales_person_list = SysHelper::get_staff_list();
            if (session('logged_session_data.company_id') == 1) {
                $company = SysCompany::select('id', 'company_name')->orderBy('sort_id', 'asc')->get();
            } else {
                $company = SysCompany::select('id', 'company_name')
                    ->where('id', session('logged_session_data.company_id'))
                    ->orderBy('sort_id', 'asc')
                    ->get();
            }

            return view('backEnd.inventory.InventoryCategoryWiseReport', compact(
                'category_report_rows',
                'from_date',
                'to_date',
                'ctrl_supplier',
                'ctrl_sales_person',
                'ctrl_company',
                'supplier_list',
                'sales_person_list',
                'company',
                'grand_qty',
                'grand_avg_rate',
                'grand_value',
                'grand_discount',
                'grand_taxableamount',
                'grand_vatamount',
                'grand_total_amount',
                'grand_total_amount_si',
                'grand_total_amount_sr',
                'grand_qty_si',
                'grand_qty_sr',
                'grand_avg_rate_si',
                'grand_avg_rate_sr',
                'grand_value_si',
                'grand_value_sr',
                'grand_discount_si',
                'grand_discount_sr',
                'grand_taxable_si',
                'grand_taxable_sr',
                'grand_vat_si',
                'grand_vat_sr',
                'grand_si_doc_count',
                'grand_sr_doc_count'
            ));
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function inventory_report_subcategory_wise(Request $request)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $from_date = date('Y-01-01');
            $to_date = date('Y-m-d');
            $ctrl_supplier = '';
            $ctrl_sales_person = '';
            $ctrl_company = session('logged_session_data.company_id');

            if ($request->isMethod('post')) {
                $to_date = SysHelper::normalizeToYmd($request->to_date);
                if ($to_date == '') {
                    $to_date = date('Y-m-d');
                }
                $from_date = SysHelper::normalizeToYmd($request->from_date);
                if ($from_date == '') {
                    $from_date = date('Y-m-d');
                }
            } else {
                if ($request->filled('from_date')) {
                    $fd = SysHelper::normalizeToYmd($request->input('from_date'));
                    if ($fd !== '') {
                        $from_date = $fd;
                    }
                }
                if ($request->filled('to_date')) {
                    $td = SysHelper::normalizeToYmd($request->input('to_date'));
                    if ($td !== '') {
                        $to_date = $td;
                    }
                }
            }

            if ($request->filled('company')) {
                $ctrl_company = $request->company;
            }
            if ($request->filled('supplier')) {
                $ctrl_supplier = $request->supplier;
            }
            if ($request->filled('sales_person')) {
                $ctrl_sales_person = $request->sales_person;
            }

            $dateFrom = date('Y-m-d', strtotime($from_date));
            $dateTo = date('Y-m-d', strtotime($to_date));

            $subCategoryRowsSi = DB::table('sys_sales_invoice_items as si')
                ->select(
                    'subcat.id as sub_category_id',
                    DB::raw('COALESCE(subcat.sub_category_name, "Uncategorized") as sub_category_name'),
                    DB::raw('SUM(si.qty) as qty'),
                    DB::raw('SUM(si.value) as value'),
                    DB::raw('SUM(si.discount) as discount'),
                    DB::raw('SUM(si.taxableamount) as taxableamount'),
                    DB::raw('SUM(si.vatamount) as vatamount'),
                    DB::raw('SUM(si.taxableamount + si.vatamount) as total_amount')
                )
                ->join('sys_sales_invoice as s', 's.id', '=', 'si.si_id')
                ->join('sm_items as i', 'i.id', '=', 'si.part_number')
                ->leftJoin('sm_item_subcategories as subcat', 'subcat.id', '=', 'i.subcategory_name')
                ->where('s.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(s.doc_date)'), [$dateFrom, $dateTo]);

            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $subCategoryRowsSi->where('s.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $subCategoryRowsSi->where('s.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $subCategoryRowsSi->where('s.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $subCategoryRowsSi->where('s.sales_man', Auth::user()->id);
            }

            $subCategoryRowsSi = $subCategoryRowsSi->groupBy('subcat.id', 'subcat.sub_category_name')->get()->keyBy(function ($r) {
                return $r->sub_category_id === null ? '_null_' : (string) $r->sub_category_id;
            });

            $subCategoryRowsSr = DB::table('sys_sales_return_list as srl')
                ->select(
                    'subcat.id as sub_category_id',
                    DB::raw('COALESCE(subcat.sub_category_name, "Uncategorized") as sub_category_name'),
                    DB::raw('SUM(srl.qty) as qty'),
                    DB::raw('SUM(srl.value) as value'),
                    DB::raw('SUM(srl.discount) as discount'),
                    DB::raw('SUM(srl.taxableamount) as taxableamount'),
                    DB::raw('SUM(srl.vatamount) as vatamount'),
                    DB::raw('SUM(srl.taxableamount + srl.vatamount) as total_amount')
                )
                ->join('sys_sales_return as sr', 'sr.id', '=', 'srl.sr_id')
                ->join('sm_items as i', 'i.id', '=', 'srl.part_number')
                ->leftJoin('sm_item_subcategories as subcat', 'subcat.id', '=', 'i.subcategory_name')
                ->where('sr.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(sr.doc_date)'), [$dateFrom, $dateTo]);

            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $subCategoryRowsSr->where('sr.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $subCategoryRowsSr->where('sr.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $subCategoryRowsSr->where('sr.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $subCategoryRowsSr->where('sr.sales_man', Auth::user()->id);
            }

            $subCategoryRowsSr = $subCategoryRowsSr->groupBy('subcat.id', 'subcat.sub_category_name')->get()->keyBy(function ($r) {
                return $r->sub_category_id === null ? '_null_' : (string) $r->sub_category_id;
            });

            $siDocs = DB::table('sys_sales_invoice_items as si')
                ->select('subcat.id as sub_category_id', DB::raw('COUNT(DISTINCT s.id) as doc_cnt'))
                ->join('sys_sales_invoice as s', 's.id', '=', 'si.si_id')
                ->join('sm_items as i', 'i.id', '=', 'si.part_number')
                ->leftJoin('sm_item_subcategories as subcat', 'subcat.id', '=', 'i.subcategory_name')
                ->where('s.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(s.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $siDocs->where('s.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $siDocs->where('s.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $siDocs->where('s.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $siDocs->where('s.sales_man', Auth::user()->id);
            }
            $siDocs = $siDocs->groupBy('subcat.id')->get()->keyBy(function ($r) {
                return $r->sub_category_id === null ? '_null_' : (string) $r->sub_category_id;
            });

            $srDocs = DB::table('sys_sales_return_list as srl')
                ->select('subcat.id as sub_category_id', DB::raw('COUNT(DISTINCT sr.id) as doc_cnt'))
                ->join('sys_sales_return as sr', 'sr.id', '=', 'srl.sr_id')
                ->join('sm_items as i', 'i.id', '=', 'srl.part_number')
                ->leftJoin('sm_item_subcategories as subcat', 'subcat.id', '=', 'i.subcategory_name')
                ->where('sr.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(sr.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $srDocs->where('sr.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $srDocs->where('sr.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $srDocs->where('sr.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $srDocs->where('sr.sales_man', Auth::user()->id);
            }
            $srDocs = $srDocs->groupBy('subcat.id')->get()->keyBy(function ($r) {
                return $r->sub_category_id === null ? '_null_' : (string) $r->sub_category_id;
            });

            $grandSiDocQ = DB::table('sys_sales_invoice as s')
                ->join('sys_sales_invoice_items as si', 'si.si_id', '=', 's.id')
                ->join('sm_items as i', 'i.id', '=', 'si.part_number')
                ->where('s.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(s.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $grandSiDocQ->where('s.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $grandSiDocQ->where('s.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $grandSiDocQ->where('s.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $grandSiDocQ->where('s.sales_man', Auth::user()->id);
            }
            $grand_si_doc_count = (int) $grandSiDocQ->select(DB::raw('COUNT(DISTINCT s.id) as c'))->value('c');

            $grandSrDocQ = DB::table('sys_sales_return as sr')
                ->join('sys_sales_return_list as srl', 'srl.sr_id', '=', 'sr.id')
                ->join('sm_items as i', 'i.id', '=', 'srl.part_number')
                ->where('sr.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(sr.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $grandSrDocQ->where('sr.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $grandSrDocQ->where('sr.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $grandSrDocQ->where('sr.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $grandSrDocQ->where('sr.sales_man', Auth::user()->id);
            }
            $grand_sr_doc_count = (int) $grandSrDocQ->select(DB::raw('COUNT(DISTINCT sr.id) as c'))->value('c');

            $sk = $subCategoryRowsSi->keys()->merge($subCategoryRowsSr->keys())->unique()->sort()->values();
            $subcategory_report_rows = [];
            $grand_qty = 0;
            $grand_value = 0;
            $grand_discount = 0;
            $grand_taxableamount = 0;
            $grand_vatamount = 0;
            $grand_total_amount = 0;
            $grand_qty_si = 0;
            $grand_qty_sr = 0;
            $grand_value_si = 0;
            $grand_value_sr = 0;
            $grand_discount_si = 0;
            $grand_discount_sr = 0;
            $grand_taxable_si = 0;
            $grand_taxable_sr = 0;
            $grand_vat_si = 0;
            $grand_vat_sr = 0;
            foreach ($sk as $k) {
                $siR = $subCategoryRowsSi->get($k);
                $srR = $subCategoryRowsSr->get($k);
                $qtySi = (float) (optional($siR)->qty ?? 0);
                $qtySr = (float) (optional($srR)->qty ?? 0);
                $valueSi = (float) (optional($siR)->value ?? 0);
                $valueSr = (float) (optional($srR)->value ?? 0);
                $discSi = (float) (optional($siR)->discount ?? 0);
                $discSr = (float) (optional($srR)->discount ?? 0);
                $taxSi = (float) (optional($siR)->taxableamount ?? 0);
                $taxSr = (float) (optional($srR)->taxableamount ?? 0);
                $vatSi = (float) (optional($siR)->vatamount ?? 0);
                $vatSr = (float) (optional($srR)->vatamount ?? 0);
                $qty = $qtySi - $qtySr;
                $value = $valueSi - $valueSr;
                $discount = $discSi - $discSr;
                $taxableamount = $taxSi - $taxSr;
                $vatamount = $vatSi - $vatSr;
                $total_amount = $taxableamount + $vatamount;
                $total_amount_si = $taxSi + $vatSi;
                $total_amount_sr = $taxSr + $vatSr;
                $avg_rate = $qty > 0 ? $value / $qty : 0;
                $sid = optional($siR)->sub_category_id ?? optional($srR)->sub_category_id;
                $sname = optional($siR)->sub_category_name ?? optional($srR)->sub_category_name;
                $siDoc = (int) (optional($siDocs->get($k))->doc_cnt ?? 0);
                $srDoc = (int) (optional($srDocs->get($k))->doc_cnt ?? 0);

                $subcategory_report_rows[] = (object) [
                    'sub_category_id' => $sid,
                    'sub_category_name' => $sname,
                    'qty' => $qty,
                    'avg_rate' => $avg_rate,
                    'value' => $value,
                    'discount' => $discount,
                    'taxableamount' => $taxableamount,
                    'vatamount' => $vatamount,
                    'total_amount' => $total_amount,
                    'total_amount_si' => $total_amount_si,
                    'total_amount_sr' => $total_amount_sr,
                    'si_doc_count' => $siDoc,
                    'sr_doc_count' => $srDoc,
                ];

                $grand_qty += $qty;
                $grand_value += $value;
                $grand_discount += $discount;
                $grand_taxableamount += $taxableamount;
                $grand_vatamount += $vatamount;
                $grand_total_amount += $total_amount;
                $grand_qty_si += $qtySi;
                $grand_qty_sr += $qtySr;
                $grand_value_si += $valueSi;
                $grand_value_sr += $valueSr;
                $grand_discount_si += $discSi;
                $grand_discount_sr += $discSr;
                $grand_taxable_si += $taxSi;
                $grand_taxable_sr += $taxSr;
                $grand_vat_si += $vatSi;
                $grand_vat_sr += $vatSr;
            }
            usort($subcategory_report_rows, function ($a, $b) {
                return strcmp($a->sub_category_name, $b->sub_category_name);
            });
            $grand_avg_rate = $grand_qty > 0 ? $grand_value / $grand_qty : 0;
            $grand_avg_rate_si = $grand_qty_si > 0 ? $grand_value_si / $grand_qty_si : 0;
            $grand_avg_rate_sr = $grand_qty_sr > 0 ? $grand_value_sr / $grand_qty_sr : 0;
            $grand_total_amount_si = $grand_taxable_si + $grand_vat_si;
            $grand_total_amount_sr = $grand_taxable_sr + $grand_vat_sr;

            $supplier_list = SysHelper::get_customer_list($company_id);
            $sales_person_list = SysHelper::get_staff_list();
            if (session('logged_session_data.company_id') == 1) {
                $company = SysCompany::select('id', 'company_name')->orderBy('sort_id', 'asc')->get();
            } else {
                $company = SysCompany::select('id', 'company_name')
                    ->where('id', session('logged_session_data.company_id'))
                    ->orderBy('sort_id', 'asc')
                    ->get();
            }

            return view('backEnd.inventory.InventorySubCategoryWiseReport', compact(
                'subcategory_report_rows',
                'from_date',
                'to_date',
                'ctrl_supplier',
                'ctrl_sales_person',
                'ctrl_company',
                'supplier_list',
                'sales_person_list',
                'company',
                'grand_qty',
                'grand_avg_rate',
                'grand_value',
                'grand_discount',
                'grand_taxableamount',
                'grand_vatamount',
                'grand_total_amount',
                'grand_total_amount_si',
                'grand_total_amount_sr',
                'grand_qty_si',
                'grand_qty_sr',
                'grand_avg_rate_si',
                'grand_avg_rate_sr',
                'grand_value_si',
                'grand_value_sr',
                'grand_discount_si',
                'grand_discount_sr',
                'grand_taxable_si',
                'grand_taxable_sr',
                'grand_vat_si',
                'grand_vat_sr',
                'grand_si_doc_count',
                'grand_sr_doc_count'
            ));
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function inventory_report_company_wise(Request $request)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $from_date = date('Y-01-01');
            $to_date = date('Y-m-d');
            $ctrl_supplier = '';
            $ctrl_sales_person = '';
            $ctrl_company = '';

            if ($request->isMethod('post')) {
                $to_date = SysHelper::normalizeToYmd($request->to_date);
                if ($to_date == '') {
                    $to_date = date('Y-m-d');
                }
                $from_date = SysHelper::normalizeToYmd($request->from_date);
                if ($from_date == '') {
                    $from_date = date('Y-m-d');
                }
            } else {
                if ($request->filled('from_date')) {
                    $fd = SysHelper::normalizeToYmd($request->input('from_date'));
                    if ($fd !== '') {
                        $from_date = $fd;
                    }
                }
                if ($request->filled('to_date')) {
                    $td = SysHelper::normalizeToYmd($request->input('to_date'));
                    if ($td !== '') {
                        $to_date = $td;
                    }
                }
            }

            if ($request->filled('company')) {
                $ctrl_company = $request->company;
            }
            if ($request->filled('supplier')) {
                $ctrl_supplier = $request->supplier;
            }
            if ($request->filled('sales_person')) {
                $ctrl_sales_person = $request->sales_person;
            }

            if (session('logged_session_data.company_id') == 1) {
                $company = SysCompany::select('id', 'company_name')->orderBy('sort_id', 'asc')->get();
            } else {
                $company = SysCompany::select('id', 'company_name')
                    ->where('id', session('logged_session_data.company_id'))
                    ->orderBy('sort_id', 'asc')
                    ->get();
            }

            $dateFrom = date('Y-m-d', strtotime($from_date));
            $dateTo = date('Y-m-d', strtotime($to_date));

            $companyAggSi = DB::table('sys_sales_invoice_items as si')
                ->select(
                    's.company_id',
                    DB::raw('SUM(si.qty) as qty'),
                    DB::raw('SUM(si.value) as value'),
                    DB::raw('SUM(si.discount) as discount'),
                    DB::raw('SUM(si.taxableamount) as taxableamount'),
                    DB::raw('SUM(si.vatamount) as vatamount'),
                    DB::raw('SUM(si.taxableamount + si.vatamount) as total_amount')
                )
                ->join('sys_sales_invoice as s', 's.id', '=', 'si.si_id')
                ->join('sm_items as i', 'i.id', '=', 'si.part_number')
                ->where('s.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(s.doc_date)'), [$dateFrom, $dateTo]);

            if (!empty($ctrl_company)) {
                $companyAggSi->where('s.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $companyAggSi->where('s.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $companyAggSi->where('s.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $companyAggSi->where('s.sales_man', Auth::user()->id);
            }

            $companyAggSi = $companyAggSi->groupBy('s.company_id')->get()->keyBy('company_id');

            $companyAggSr = DB::table('sys_sales_return_list as srl')
                ->select(
                    'sr.company_id',
                    DB::raw('SUM(srl.qty) as qty'),
                    DB::raw('SUM(srl.value) as value'),
                    DB::raw('SUM(srl.discount) as discount'),
                    DB::raw('SUM(srl.taxableamount) as taxableamount'),
                    DB::raw('SUM(srl.vatamount) as vatamount'),
                    DB::raw('SUM(srl.taxableamount + srl.vatamount) as total_amount')
                )
                ->join('sys_sales_return as sr', 'sr.id', '=', 'srl.sr_id')
                ->join('sm_items as i', 'i.id', '=', 'srl.part_number')
                ->where('sr.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(sr.doc_date)'), [$dateFrom, $dateTo]);

            if (!empty($ctrl_company)) {
                $companyAggSr->where('sr.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $companyAggSr->where('sr.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $companyAggSr->where('sr.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $companyAggSr->where('sr.sales_man', Auth::user()->id);
            }

            $companyAggSr = $companyAggSr->groupBy('sr.company_id')->get()->keyBy('company_id');

            $siDocs = DB::table('sys_sales_invoice_items as si')
                ->select('s.company_id', DB::raw('COUNT(DISTINCT s.id) as doc_cnt'))
                ->join('sys_sales_invoice as s', 's.id', '=', 'si.si_id')
                ->join('sm_items as i', 'i.id', '=', 'si.part_number')
                ->where('s.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(s.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company)) {
                $siDocs->where('s.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $siDocs->where('s.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $siDocs->where('s.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $siDocs->where('s.sales_man', Auth::user()->id);
            }
            $siDocs = $siDocs->groupBy('s.company_id')->get()->keyBy('company_id');

            $srDocs = DB::table('sys_sales_return_list as srl')
                ->select('sr.company_id', DB::raw('COUNT(DISTINCT sr.id) as doc_cnt'))
                ->join('sys_sales_return as sr', 'sr.id', '=', 'srl.sr_id')
                ->join('sm_items as i', 'i.id', '=', 'srl.part_number')
                ->where('sr.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(sr.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company)) {
                $srDocs->where('sr.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $srDocs->where('sr.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $srDocs->where('sr.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $srDocs->where('sr.sales_man', Auth::user()->id);
            }
            $srDocs = $srDocs->groupBy('sr.company_id')->get()->keyBy('company_id');

            $grandSiDocQ = DB::table('sys_sales_invoice as s')
                ->join('sys_sales_invoice_items as si', 'si.si_id', '=', 's.id')
                ->join('sm_items as i', 'i.id', '=', 'si.part_number')
                ->where('s.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(s.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company)) {
                $grandSiDocQ->where('s.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $grandSiDocQ->where('s.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $grandSiDocQ->where('s.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $grandSiDocQ->where('s.sales_man', Auth::user()->id);
            }
            $grand_si_doc_count = (int) $grandSiDocQ->select(DB::raw('COUNT(DISTINCT s.id) as c'))->value('c');

            $grandSrDocQ = DB::table('sys_sales_return as sr')
                ->join('sys_sales_return_list as srl', 'srl.sr_id', '=', 'sr.id')
                ->join('sm_items as i', 'i.id', '=', 'srl.part_number')
                ->where('sr.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(sr.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company)) {
                $grandSrDocQ->where('sr.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $grandSrDocQ->where('sr.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $grandSrDocQ->where('sr.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $grandSrDocQ->where('sr.sales_man', Auth::user()->id);
            }
            $grand_sr_doc_count = (int) $grandSrDocQ->select(DB::raw('COUNT(DISTINCT sr.id) as c'))->value('c');

            $company_report_rows = [];
            $grand_qty = 0;
            $grand_value = 0;
            $grand_discount = 0;
            $grand_taxableamount = 0;
            $grand_vatamount = 0;
            $grand_total_amount = 0;
            $grand_qty_si = 0;
            $grand_qty_sr = 0;
            $grand_value_si = 0;
            $grand_value_sr = 0;
            $grand_discount_si = 0;
            $grand_discount_sr = 0;
            $grand_taxable_si = 0;
            $grand_taxable_sr = 0;
            $grand_vat_si = 0;
            $grand_vat_sr = 0;

            foreach ($company as $co) {
                $siR = $companyAggSi->get($co->id);
                $srR = $companyAggSr->get($co->id);
                $qtySi = (float) (optional($siR)->qty ?? 0);
                $qtySr = (float) (optional($srR)->qty ?? 0);
                $valueSi = (float) (optional($siR)->value ?? 0);
                $valueSr = (float) (optional($srR)->value ?? 0);
                $discSi = (float) (optional($siR)->discount ?? 0);
                $discSr = (float) (optional($srR)->discount ?? 0);
                $taxSi = (float) (optional($siR)->taxableamount ?? 0);
                $taxSr = (float) (optional($srR)->taxableamount ?? 0);
                $vatSi = (float) (optional($siR)->vatamount ?? 0);
                $vatSr = (float) (optional($srR)->vatamount ?? 0);
                $qty = $qtySi - $qtySr;
                $value = $valueSi - $valueSr;
                $discount = $discSi - $discSr;
                $taxableamount = $taxSi - $taxSr;
                $vatamount = $vatSi - $vatSr;
                $total_amount = $taxableamount + $vatamount;
                $total_amount_si = $taxSi + $vatSi;
                $total_amount_sr = $taxSr + $vatSr;
                $avg_rate = $qty > 0 ? $value / $qty : 0;
                $siDoc = (int) (optional($siDocs->get($co->id))->doc_cnt ?? 0);
                $srDoc = (int) (optional($srDocs->get($co->id))->doc_cnt ?? 0);

                $company_report_rows[] = (object) [
                    'company_id' => (int) $co->id,
                    'company_name' => $co->company_name,
                    'qty' => $qty,
                    'avg_rate' => $avg_rate,
                    'value' => $value,
                    'discount' => $discount,
                    'taxableamount' => $taxableamount,
                    'vatamount' => $vatamount,
                    'total_amount' => $total_amount,
                    'total_amount_si' => $total_amount_si,
                    'total_amount_sr' => $total_amount_sr,
                    'si_doc_count' => $siDoc,
                    'sr_doc_count' => $srDoc,
                ];

                $grand_qty += $qty;
                $grand_value += $value;
                $grand_discount += $discount;
                $grand_taxableamount += $taxableamount;
                $grand_vatamount += $vatamount;
                $grand_total_amount += $total_amount;
                $grand_qty_si += $qtySi;
                $grand_qty_sr += $qtySr;
                $grand_value_si += $valueSi;
                $grand_value_sr += $valueSr;
                $grand_discount_si += $discSi;
                $grand_discount_sr += $discSr;
                $grand_taxable_si += $taxSi;
                $grand_taxable_sr += $taxSr;
                $grand_vat_si += $vatSi;
                $grand_vat_sr += $vatSr;
            }
            $grand_avg_rate = $grand_qty > 0 ? $grand_value / $grand_qty : 0;
            $grand_avg_rate_si = $grand_qty_si > 0 ? $grand_value_si / $grand_qty_si : 0;
            $grand_avg_rate_sr = $grand_qty_sr > 0 ? $grand_value_sr / $grand_qty_sr : 0;
            $grand_total_amount_si = $grand_taxable_si + $grand_vat_si;
            $grand_total_amount_sr = $grand_taxable_sr + $grand_vat_sr;

            $supplier_list = SysHelper::get_customer_list($company_id);
            $sales_person_list = SysHelper::get_staff_list();

            return view('backEnd.inventory.InventoryCompanyWiseReport', compact(
                'company_report_rows',
                'from_date',
                'to_date',
                'ctrl_supplier',
                'ctrl_sales_person',
                'ctrl_company',
                'supplier_list',
                'sales_person_list',
                'company',
                'grand_qty',
                'grand_avg_rate',
                'grand_value',
                'grand_discount',
                'grand_taxableamount',
                'grand_vatamount',
                'grand_total_amount',
                'grand_total_amount_si',
                'grand_total_amount_sr',
                'grand_qty_si',
                'grand_qty_sr',
                'grand_avg_rate_si',
                'grand_avg_rate_sr',
                'grand_value_si',
                'grand_value_sr',
                'grand_discount_si',
                'grand_discount_sr',
                'grand_taxable_si',
                'grand_taxable_sr',
                'grand_vat_si',
                'grand_vat_sr',
                'grand_si_doc_count',
                'grand_sr_doc_count'
            ));
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function inventory_report_salesperson_wise(Request $request)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $from_date = date('Y-01-01');
            $to_date = date('Y-m-d');
            $ctrl_supplier = '';
            $ctrl_sales_person = '';
            $ctrl_company = '';

            if ($request->isMethod('post')) {
                $to_date = SysHelper::normalizeToYmd($request->to_date);
                if ($to_date == '') {
                    $to_date = date('Y-m-d');
                }
                $from_date = SysHelper::normalizeToYmd($request->from_date);
                if ($from_date == '') {
                    $from_date = date('Y-m-d');
                }
            } else {
                if ($request->filled('from_date')) {
                    $fd = SysHelper::normalizeToYmd($request->input('from_date'));
                    if ($fd !== '') {
                        $from_date = $fd;
                    }
                }
                if ($request->filled('to_date')) {
                    $td = SysHelper::normalizeToYmd($request->input('to_date'));
                    if ($td !== '') {
                        $to_date = $td;
                    }
                }
            }

            if ($request->filled('company')) {
                $ctrl_company = $request->company;
            }
            if ($request->filled('supplier')) {
                $ctrl_supplier = $request->supplier;
            }
            if ($request->filled('sales_person')) {
                $ctrl_sales_person = $request->sales_person;
            }

            $dateFrom = date('Y-m-d', strtotime($from_date));
            $dateTo = date('Y-m-d', strtotime($to_date));

            $siSpIds = DB::table('sys_sales_invoice as s')
                ->join('sys_sales_invoice_items as si', 'si.si_id', '=', 's.id')
                ->join('sm_items as i', 'i.id', '=', 'si.part_number')
                ->where('s.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(s.doc_date)'), [$dateFrom, $dateTo])
                ->whereNotNull('s.sales_man')
                ->where('s.sales_man', '!=', 0);
            if (session('logged_session_data.company_id') != 1) {
                $siSpIds->where('s.company_id', session('logged_session_data.company_id'));
            }
            if (!empty($ctrl_company)) {
                $siSpIds->where('s.company_id', $ctrl_company);
            }
            if (!empty($ctrl_supplier)) {
                $siSpIds->where('s.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $siSpIds->where('s.sales_man', Auth::user()->id);
            }
            if (!empty($ctrl_sales_person)) {
                $siSpIds->where('s.sales_man', $ctrl_sales_person);
            }
            $siUserIds = $siSpIds->distinct()->pluck('s.sales_man');

            $srSpIds = DB::table('sys_sales_return as sr')
                ->join('sys_sales_return_list as srl', 'srl.sr_id', '=', 'sr.id')
                ->join('sm_items as i', 'i.id', '=', 'srl.part_number')
                ->where('sr.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(sr.doc_date)'), [$dateFrom, $dateTo])
                ->whereNotNull('sr.sales_man')
                ->where('sr.sales_man', '!=', 0);
            if (session('logged_session_data.company_id') != 1) {
                $srSpIds->where('sr.company_id', session('logged_session_data.company_id'));
            }
            if (!empty($ctrl_company)) {
                $srSpIds->where('sr.company_id', $ctrl_company);
            }
            if (!empty($ctrl_supplier)) {
                $srSpIds->where('sr.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $srSpIds->where('sr.sales_man', Auth::user()->id);
            }
            if (!empty($ctrl_sales_person)) {
                $srSpIds->where('sr.sales_man', $ctrl_sales_person);
            }
            $srUserIds = $srSpIds->distinct()->pluck('sr.sales_man');

            $staffIds = $siUserIds->merge($srUserIds)->unique()->filter()->values();
            if ($staffIds->isEmpty()) {
                $sales_person_list = collect();
            } else {
                $sales_person_list = DB::table('sm_staffs as u')
                    ->select('u.user_id', 'u.full_name')
                    ->whereIn('u.user_id', $staffIds->all())
                    ->orderBy('u.full_name', 'asc')
                    ->get();
            }

            if (session('logged_session_data.company_id') == 1) {
                $company = SysCompany::select('id', 'company_name')->orderBy('sort_id', 'asc')->get();
            } else {
                $company = SysCompany::select('id', 'company_name')
                    ->where('id', session('logged_session_data.company_id'))
                    ->orderBy('sort_id', 'asc')
                    ->get();
            }

            $supplier_list = SysHelper::get_customer_list($company_id);

            $salesAggSi = DB::table('sys_sales_invoice_items as si')
                ->select(
                    's.sales_man',
                    DB::raw('SUM(si.qty) as qty'),
                    DB::raw('SUM(si.value) as value'),
                    DB::raw('SUM(si.discount) as discount'),
                    DB::raw('SUM(si.taxableamount) as taxableamount'),
                    DB::raw('SUM(si.vatamount) as vatamount'),
                    DB::raw('SUM(si.taxableamount + si.vatamount) as total_amount')
                )
                ->join('sys_sales_invoice as s', 's.id', '=', 'si.si_id')
                ->join('sm_items as i', 'i.id', '=', 'si.part_number')
                ->where('s.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(s.doc_date)'), [$dateFrom, $dateTo]);

            if (!empty($ctrl_company)) {
                $salesAggSi->where('s.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $salesAggSi->where('s.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $salesAggSi->where('s.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $salesAggSi->where('s.sales_man', Auth::user()->id);
            }

            $salesAggSi = $salesAggSi->groupBy('s.sales_man')->get()->keyBy('sales_man');

            $salesAggSr = DB::table('sys_sales_return_list as srl')
                ->select(
                    'sr.sales_man',
                    DB::raw('SUM(srl.qty) as qty'),
                    DB::raw('SUM(srl.value) as value'),
                    DB::raw('SUM(srl.discount) as discount'),
                    DB::raw('SUM(srl.taxableamount) as taxableamount'),
                    DB::raw('SUM(srl.vatamount) as vatamount'),
                    DB::raw('SUM(srl.taxableamount + srl.vatamount) as total_amount')
                )
                ->join('sys_sales_return as sr', 'sr.id', '=', 'srl.sr_id')
                ->join('sm_items as i', 'i.id', '=', 'srl.part_number')
                ->where('sr.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(sr.doc_date)'), [$dateFrom, $dateTo]);

            if (!empty($ctrl_company)) {
                $salesAggSr->where('sr.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $salesAggSr->where('sr.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $salesAggSr->where('sr.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $salesAggSr->where('sr.sales_man', Auth::user()->id);
            }

            $salesAggSr = $salesAggSr->groupBy('sr.sales_man')->get()->keyBy('sales_man');

            $siDocs = DB::table('sys_sales_invoice_items as si')
                ->select('s.sales_man', DB::raw('COUNT(DISTINCT s.id) as doc_cnt'))
                ->join('sys_sales_invoice as s', 's.id', '=', 'si.si_id')
                ->join('sm_items as i', 'i.id', '=', 'si.part_number')
                ->where('s.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(s.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company)) {
                $siDocs->where('s.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $siDocs->where('s.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $siDocs->where('s.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $siDocs->where('s.sales_man', Auth::user()->id);
            }
            $siDocs = $siDocs->groupBy('s.sales_man')->get()->keyBy('sales_man');

            $srDocs = DB::table('sys_sales_return_list as srl')
                ->select('sr.sales_man', DB::raw('COUNT(DISTINCT sr.id) as doc_cnt'))
                ->join('sys_sales_return as sr', 'sr.id', '=', 'srl.sr_id')
                ->join('sm_items as i', 'i.id', '=', 'srl.part_number')
                ->where('sr.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(sr.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company)) {
                $srDocs->where('sr.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $srDocs->where('sr.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $srDocs->where('sr.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $srDocs->where('sr.sales_man', Auth::user()->id);
            }
            $srDocs = $srDocs->groupBy('sr.sales_man')->get()->keyBy('sales_man');

            $grandSiDocQ = DB::table('sys_sales_invoice as s')
                ->join('sys_sales_invoice_items as si', 'si.si_id', '=', 's.id')
                ->join('sm_items as i', 'i.id', '=', 'si.part_number')
                ->where('s.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(s.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company)) {
                $grandSiDocQ->where('s.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $grandSiDocQ->where('s.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $grandSiDocQ->where('s.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $grandSiDocQ->where('s.sales_man', Auth::user()->id);
            }
            $grand_si_doc_count = (int) $grandSiDocQ->select(DB::raw('COUNT(DISTINCT s.id) as c'))->value('c');

            $grandSrDocQ = DB::table('sys_sales_return as sr')
                ->join('sys_sales_return_list as srl', 'srl.sr_id', '=', 'sr.id')
                ->join('sm_items as i', 'i.id', '=', 'srl.part_number')
                ->where('sr.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(sr.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company)) {
                $grandSrDocQ->where('sr.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $grandSrDocQ->where('sr.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $grandSrDocQ->where('sr.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $grandSrDocQ->where('sr.sales_man', Auth::user()->id);
            }
            $grand_sr_doc_count = (int) $grandSrDocQ->select(DB::raw('COUNT(DISTINCT sr.id) as c'))->value('c');

            $salesperson_report_rows = [];
            $grand_qty = 0;
            $grand_value = 0;
            $grand_discount = 0;
            $grand_taxableamount = 0;
            $grand_vatamount = 0;
            $grand_total_amount = 0;
            $grand_qty_si = 0;
            $grand_qty_sr = 0;
            $grand_value_si = 0;
            $grand_value_sr = 0;
            $grand_discount_si = 0;
            $grand_discount_sr = 0;
            $grand_taxable_si = 0;
            $grand_taxable_sr = 0;
            $grand_vat_si = 0;
            $grand_vat_sr = 0;

            foreach ($sales_person_list as $sp) {
                $uid = (int) $sp->user_id;
                $siR = $salesAggSi->get($uid);
                $srR = $salesAggSr->get($uid);
                $qtySi = (float) (optional($siR)->qty ?? 0);
                $qtySr = (float) (optional($srR)->qty ?? 0);
                $valueSi = (float) (optional($siR)->value ?? 0);
                $valueSr = (float) (optional($srR)->value ?? 0);
                $discSi = (float) (optional($siR)->discount ?? 0);
                $discSr = (float) (optional($srR)->discount ?? 0);
                $taxSi = (float) (optional($siR)->taxableamount ?? 0);
                $taxSr = (float) (optional($srR)->taxableamount ?? 0);
                $vatSi = (float) (optional($siR)->vatamount ?? 0);
                $vatSr = (float) (optional($srR)->vatamount ?? 0);
                $qty = $qtySi - $qtySr;
                $value = $valueSi - $valueSr;
                $discount = $discSi - $discSr;
                $taxableamount = $taxSi - $taxSr;
                $vatamount = $vatSi - $vatSr;
                $total_amount = $taxableamount + $vatamount;
                $total_amount_si = $taxSi + $vatSi;
                $total_amount_sr = $taxSr + $vatSr;
                $avg_rate = $qty > 0 ? $value / $qty : 0;
                $siDoc = (int) (optional($siDocs->get($uid))->doc_cnt ?? 0);
                $srDoc = (int) (optional($srDocs->get($uid))->doc_cnt ?? 0);

                $salesperson_report_rows[] = (object) [
                    'sales_person_id' => $uid,
                    'sales_person_name' => $sp->full_name,
                    'qty' => $qty,
                    'avg_rate' => $avg_rate,
                    'value' => $value,
                    'discount' => $discount,
                    'taxableamount' => $taxableamount,
                    'vatamount' => $vatamount,
                    'total_amount' => $total_amount,
                    'total_amount_si' => $total_amount_si,
                    'total_amount_sr' => $total_amount_sr,
                    'si_doc_count' => $siDoc,
                    'sr_doc_count' => $srDoc,
                ];

                $grand_qty += $qty;
                $grand_value += $value;
                $grand_discount += $discount;
                $grand_taxableamount += $taxableamount;
                $grand_vatamount += $vatamount;
                $grand_total_amount += $total_amount;
                $grand_qty_si += $qtySi;
                $grand_qty_sr += $qtySr;
                $grand_value_si += $valueSi;
                $grand_value_sr += $valueSr;
                $grand_discount_si += $discSi;
                $grand_discount_sr += $discSr;
                $grand_taxable_si += $taxSi;
                $grand_taxable_sr += $taxSr;
                $grand_vat_si += $vatSi;
                $grand_vat_sr += $vatSr;
            }
            $grand_avg_rate = $grand_qty > 0 ? $grand_value / $grand_qty : 0;
            $grand_avg_rate_si = $grand_qty_si > 0 ? $grand_value_si / $grand_qty_si : 0;
            $grand_avg_rate_sr = $grand_qty_sr > 0 ? $grand_value_sr / $grand_qty_sr : 0;
            $grand_total_amount_si = $grand_taxable_si + $grand_vat_si;
            $grand_total_amount_sr = $grand_taxable_sr + $grand_vat_sr;

            return view('backEnd.inventory.InventorySalesPersonWiseReport', compact(
                'salesperson_report_rows',
                'from_date',
                'to_date',
                'ctrl_supplier',
                'ctrl_sales_person',
                'ctrl_company',
                'supplier_list',
                'sales_person_list',
                'company',
                'grand_qty',
                'grand_avg_rate',
                'grand_value',
                'grand_discount',
                'grand_taxableamount',
                'grand_vatamount',
                'grand_total_amount',
                'grand_total_amount_si',
                'grand_total_amount_sr',
                'grand_qty_si',
                'grand_qty_sr',
                'grand_avg_rate_si',
                'grand_avg_rate_sr',
                'grand_value_si',
                'grand_value_sr',
                'grand_discount_si',
                'grand_discount_sr',
                'grand_taxable_si',
                'grand_taxable_sr',
                'grand_vat_si',
                'grand_vat_sr',
                'grand_si_doc_count',
                'grand_sr_doc_count'
            ));
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function inventory_report_customer_wise(Request $request)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $from_date = date('Y-01-01');
            $to_date = date('Y-m-d');
            $ctrl_supplier = '';
            $ctrl_sales_person = '';
            $ctrl_company = session('logged_session_data.company_id');

            if ($request->isMethod('post')) {
                $to_date = SysHelper::normalizeToYmd($request->to_date);
                if ($to_date == '') {
                    $to_date = date('Y-m-d');
                }
                $from_date = SysHelper::normalizeToYmd($request->from_date);
                if ($from_date == '') {
                    $from_date = date('Y-m-d');
                }
            } else {
                if ($request->filled('from_date')) {
                    $fd = SysHelper::normalizeToYmd($request->input('from_date'));
                    if ($fd !== '') {
                        $from_date = $fd;
                    }
                }
                if ($request->filled('to_date')) {
                    $td = SysHelper::normalizeToYmd($request->input('to_date'));
                    if ($td !== '') {
                        $to_date = $td;
                    }
                }
            }

            if ($request->filled('company')) {
                $ctrl_company = $request->company;
            }
            if ($request->filled('supplier')) {
                $ctrl_supplier = $request->supplier;
            }
            if ($request->filled('sales_person')) {
                $ctrl_sales_person = $request->sales_person;
            }

            if (session('logged_session_data.company_id') == 1) {
                $company = SysCompany::select('id', 'company_name')->orderBy('sort_id', 'asc')->get();
            } else {
                $company = SysCompany::select('id', 'company_name')
                    ->where('id', session('logged_session_data.company_id'))
                    ->orderBy('sort_id', 'asc')
                    ->get();
            }

            $dateFrom = date('Y-m-d', strtotime($from_date));
            $dateTo = date('Y-m-d', strtotime($to_date));

            $custSi = DB::table('sys_sales_invoice_items as si')
                ->select(
                    's.customer as customer_id',
                    'c.account_name as customer_name',
                    DB::raw('SUM(si.qty) as qty'),
                    DB::raw('SUM(si.value) as value'),
                    DB::raw('SUM(si.discount) as discount'),
                    DB::raw('SUM(si.taxableamount) as taxableamount'),
                    DB::raw('SUM(si.vatamount) as vatamount'),
                    DB::raw('SUM(si.taxableamount + si.vatamount) as total_amount')
                )
                ->join('sys_sales_invoice as s', 's.id', '=', 'si.si_id')
                ->join('sm_items as i', 'i.id', '=', 'si.part_number')
                ->join('sys_chartofaccounts as c', 'c.id', '=', 's.customer')
                ->where('s.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(s.doc_date)'), [$dateFrom, $dateTo]);

            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $custSi->where('s.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $custSi->where('s.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $custSi->where('s.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $custSi->where('s.sales_man', Auth::user()->id);
            }

            $custSi = $custSi->groupBy('s.customer', 'c.account_name')->get()->keyBy('customer_id');

            $custSr = DB::table('sys_sales_return_list as srl')
                ->select(
                    'sr.customer as customer_id',
                    'c.account_name as customer_name',
                    DB::raw('SUM(srl.qty) as qty'),
                    DB::raw('SUM(srl.value) as value'),
                    DB::raw('SUM(srl.discount) as discount'),
                    DB::raw('SUM(srl.taxableamount) as taxableamount'),
                    DB::raw('SUM(srl.vatamount) as vatamount'),
                    DB::raw('SUM(srl.taxableamount + srl.vatamount) as total_amount')
                )
                ->join('sys_sales_return as sr', 'sr.id', '=', 'srl.sr_id')
                ->join('sm_items as i', 'i.id', '=', 'srl.part_number')
                ->join('sys_chartofaccounts as c', 'c.id', '=', 'sr.customer')
                ->where('sr.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(sr.doc_date)'), [$dateFrom, $dateTo]);

            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $custSr->where('sr.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $custSr->where('sr.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $custSr->where('sr.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $custSr->where('sr.sales_man', Auth::user()->id);
            }

            $custSr = $custSr->groupBy('sr.customer', 'c.account_name')->get()->keyBy('customer_id');

            $siDocs = DB::table('sys_sales_invoice_items as si')
                ->select('s.customer as customer_id', DB::raw('COUNT(DISTINCT s.id) as doc_cnt'))
                ->join('sys_sales_invoice as s', 's.id', '=', 'si.si_id')
                ->join('sm_items as i', 'i.id', '=', 'si.part_number')
                ->where('s.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(s.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $siDocs->where('s.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $siDocs->where('s.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $siDocs->where('s.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $siDocs->where('s.sales_man', Auth::user()->id);
            }
            $siDocs = $siDocs->groupBy('s.customer')->get()->keyBy('customer_id');

            $srDocs = DB::table('sys_sales_return_list as srl')
                ->select('sr.customer as customer_id', DB::raw('COUNT(DISTINCT sr.id) as doc_cnt'))
                ->join('sys_sales_return as sr', 'sr.id', '=', 'srl.sr_id')
                ->join('sm_items as i', 'i.id', '=', 'srl.part_number')
                ->where('sr.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(sr.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $srDocs->where('sr.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $srDocs->where('sr.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $srDocs->where('sr.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $srDocs->where('sr.sales_man', Auth::user()->id);
            }
            $srDocs = $srDocs->groupBy('sr.customer')->get()->keyBy('customer_id');

            $grandSiDocQ = DB::table('sys_sales_invoice as s')
                ->join('sys_sales_invoice_items as si', 'si.si_id', '=', 's.id')
                ->join('sm_items as i', 'i.id', '=', 'si.part_number')
                ->where('s.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(s.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $grandSiDocQ->where('s.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $grandSiDocQ->where('s.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $grandSiDocQ->where('s.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $grandSiDocQ->where('s.sales_man', Auth::user()->id);
            }
            $grand_si_doc_count = (int) $grandSiDocQ->select(DB::raw('COUNT(DISTINCT s.id) as c'))->value('c');

            $grandSrDocQ = DB::table('sys_sales_return as sr')
                ->join('sys_sales_return_list as srl', 'srl.sr_id', '=', 'sr.id')
                ->join('sm_items as i', 'i.id', '=', 'srl.part_number')
                ->where('sr.status', 1)
                ->where('i.status', 1)
                ->whereIn('i.product_type', [1, 2])
                ->whereBetween(DB::raw('DATE(sr.doc_date)'), [$dateFrom, $dateTo]);
            if (!empty($ctrl_company) && (int) $ctrl_company !== 1) {
                $grandSrDocQ->where('sr.company_id', $ctrl_company);
            }
            if (!empty($ctrl_sales_person)) {
                $grandSrDocQ->where('sr.sales_man', $ctrl_sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $grandSrDocQ->where('sr.customer', $ctrl_supplier);
            }
            if (Auth::user()->role_id != 1) {
                $grandSrDocQ->where('sr.sales_man', Auth::user()->id);
            }
            $grand_sr_doc_count = (int) $grandSrDocQ->select(DB::raw('COUNT(DISTINCT sr.id) as c'))->value('c');

            $custKeys = $custSi->keys()->merge($custSr->keys())->unique()->sort()->values();
            $customer_report_rows = [];
            $grand_qty = 0;
            $grand_value = 0;
            $grand_discount = 0;
            $grand_taxableamount = 0;
            $grand_vatamount = 0;
            $grand_total_amount = 0;
            $grand_qty_si = 0;
            $grand_qty_sr = 0;
            $grand_value_si = 0;
            $grand_value_sr = 0;
            $grand_discount_si = 0;
            $grand_discount_sr = 0;
            $grand_taxable_si = 0;
            $grand_taxable_sr = 0;
            $grand_vat_si = 0;
            $grand_vat_sr = 0;
            foreach ($custKeys as $cid) {
                $siR = $custSi->get($cid);
                $srR = $custSr->get($cid);
                $qtySi = (float) (optional($siR)->qty ?? 0);
                $qtySr = (float) (optional($srR)->qty ?? 0);
                $valueSi = (float) (optional($siR)->value ?? 0);
                $valueSr = (float) (optional($srR)->value ?? 0);
                $discSi = (float) (optional($siR)->discount ?? 0);
                $discSr = (float) (optional($srR)->discount ?? 0);
                $taxSi = (float) (optional($siR)->taxableamount ?? 0);
                $taxSr = (float) (optional($srR)->taxableamount ?? 0);
                $vatSi = (float) (optional($siR)->vatamount ?? 0);
                $vatSr = (float) (optional($srR)->vatamount ?? 0);
                $qty = $qtySi - $qtySr;
                $value = $valueSi - $valueSr;
                $discount = $discSi - $discSr;
                $taxableamount = $taxSi - $taxSr;
                $vatamount = $vatSi - $vatSr;
                $total_amount = $taxableamount + $vatamount;
                $total_amount_si = $taxSi + $vatSi;
                $total_amount_sr = $taxSr + $vatSr;
                $avg_rate = $qty > 0 ? $value / $qty : 0;
                $cname = optional($siR)->customer_name ?? optional($srR)->customer_name;
                $siDoc = (int) (optional($siDocs->get($cid))->doc_cnt ?? 0);
                $srDoc = (int) (optional($srDocs->get($cid))->doc_cnt ?? 0);

                $customer_report_rows[] = (object) [
                    'customer_id' => (int) $cid,
                    'customer_name' => $cname,
                    'qty' => $qty,
                    'avg_rate' => $avg_rate,
                    'value' => $value,
                    'discount' => $discount,
                    'taxableamount' => $taxableamount,
                    'vatamount' => $vatamount,
                    'total_amount' => $total_amount,
                    'total_amount_si' => $total_amount_si,
                    'total_amount_sr' => $total_amount_sr,
                    'si_doc_count' => $siDoc,
                    'sr_doc_count' => $srDoc,
                ];

                $grand_qty += $qty;
                $grand_value += $value;
                $grand_discount += $discount;
                $grand_taxableamount += $taxableamount;
                $grand_vatamount += $vatamount;
                $grand_total_amount += $total_amount;
                $grand_qty_si += $qtySi;
                $grand_qty_sr += $qtySr;
                $grand_value_si += $valueSi;
                $grand_value_sr += $valueSr;
                $grand_discount_si += $discSi;
                $grand_discount_sr += $discSr;
                $grand_taxable_si += $taxSi;
                $grand_taxable_sr += $taxSr;
                $grand_vat_si += $vatSi;
                $grand_vat_sr += $vatSr;
            }
            usort($customer_report_rows, function ($a, $b) {
                return strcmp($a->customer_name, $b->customer_name);
            });
            $grand_avg_rate = $grand_qty > 0 ? $grand_value / $grand_qty : 0;
            $grand_avg_rate_si = $grand_qty_si > 0 ? $grand_value_si / $grand_qty_si : 0;
            $grand_avg_rate_sr = $grand_qty_sr > 0 ? $grand_value_sr / $grand_qty_sr : 0;
            $grand_total_amount_si = $grand_taxable_si + $grand_vat_si;
            $grand_total_amount_sr = $grand_taxable_sr + $grand_vat_sr;

            $supplier_list = SysHelper::get_customer_list($company_id);
            $sales_person_list = SysHelper::get_staff_list();

            return view('backEnd.inventory.InventoryCustomerWiseReport', compact(
                'customer_report_rows',
                'from_date',
                'to_date',
                'ctrl_supplier',
                'ctrl_sales_person',
                'ctrl_company',
                'supplier_list',
                'sales_person_list',
                'company',
                'grand_qty',
                'grand_avg_rate',
                'grand_value',
                'grand_discount',
                'grand_taxableamount',
                'grand_vatamount',
                'grand_total_amount',
                'grand_total_amount_si',
                'grand_total_amount_sr',
                'grand_qty_si',
                'grand_qty_sr',
                'grand_avg_rate_si',
                'grand_avg_rate_sr',
                'grand_value_si',
                'grand_value_sr',
                'grand_discount_si',
                'grand_discount_sr',
                'grand_taxable_si',
                'grand_taxable_sr',
                'grand_vat_si',
                'grand_vat_sr',
                'grand_si_doc_count',
                'grand_sr_doc_count'
            ));
        } catch (\Exception $e) {
            return $e;
        }
    }

    /**
     * Invoice-line detail for one item (Inventory Brand Report drill-down).
     */
    public function inventory_report_brand_detail(Request $request, $partno)
    {
        try {
            $itemRow = DB::table('sm_items as item')
                ->select(
                    'item.id',
                    'item.part_number',
                    'item.description',
                    'brand.title as brand',
                    'cat.category_name as categoryname',
                    'subcat.sub_category_name as subcategoryname',
                    'comp.company_name as company_name'
                )
                ->join('sys_brand as brand', 'brand.id', '=', 'item.brand')
                ->leftJoin('sm_item_categories as cat', 'cat.id', '=', 'item.category_name')
                ->leftJoin('sm_item_subcategories as subcat', 'subcat.id', '=', 'item.subcategory_name')
                ->leftJoin('sys_company as comp', 'comp.id', '=', 'item.company_id')
                ->where('item.id', (int) $partno)
                ->where('item.status', 1)
                ->whereIn('item.product_type', [1, 2])
                ->first();

            if (!$itemRow) {
                Toastr::error('Item not found', 'Failed');
                return redirect()->back();
            }

            $from_date = SysHelper::normalizeToYmd($request->get('from_date'));
            if ($from_date === '') {
                $from_date = date('Y-01-01');
            }
            $to_date = SysHelper::normalizeToYmd($request->get('to_date'));
            if ($to_date === '') {
                $to_date = date('Y-m-d');
            }

            $ctrl_brand = $request->get('brand', '');
            $ctrl_sales_person = $request->get('sales_person', '');
            $ctrl_supplier = $request->get('supplier', '');
            $ctrl_company = $request->get('company', session('logged_session_data.company_id'));

            $linesSi = self::sales_report_detail(
                $ctrl_brand,
                collect([$itemRow->part_number]),
                $from_date,
                $to_date,
                $ctrl_sales_person,
                $ctrl_supplier,
                $ctrl_company
            );
            $linesSr = self::sales_return_report_detail(
                $ctrl_brand,
                collect([$itemRow->part_number]),
                $from_date,
                $to_date,
                $ctrl_sales_person,
                $ctrl_supplier,
                $ctrl_company
            );
            if (!is_iterable($linesSi)) {
                $linesSi = collect();
            } else {
                $linesSi = collect($linesSi)->where('item_id', (int) $partno)->values();
            }
            if (!is_iterable($linesSr)) {
                $linesSr = collect();
            } else {
                $linesSr = collect($linesSr)->where('item_id', (int) $partno)->values();
            }

            $sumBlock = function ($coll) {
                $qty = (float) $coll->sum('qty');
                $value = (float) $coll->sum('value');
                $discount = (float) $coll->sum('discount');
                $taxableamount = (float) $coll->sum('taxableamount');
                $vatamount = (float) $coll->sum('vatamount');
                $total_amount = (float) $coll->sum(function ($li) {
                    return (float) $li->taxableamount + (float) $li->vatamount;
                });
                $avg_rate_total = $qty > 0 ? $value / $qty : 0;

                return compact('qty', 'value', 'discount', 'taxableamount', 'vatamount', 'total_amount', 'avg_rate_total');
            };
            $footer_si = $sumBlock($linesSi);
            $footer_sr = $sumBlock($linesSr);
            $footer_net = [
                'qty' => $footer_si['qty'] - $footer_sr['qty'],
                'value' => $footer_si['value'] - $footer_sr['value'],
                'discount' => $footer_si['discount'] - $footer_sr['discount'],
                'taxableamount' => $footer_si['taxableamount'] - $footer_sr['taxableamount'],
                'vatamount' => $footer_si['vatamount'] - $footer_sr['vatamount'],
                'total_amount' => $footer_si['total_amount'] - $footer_sr['total_amount'],
                'avg_rate_total' => ($footer_si['qty'] - $footer_sr['qty']) > 0
                    ? ($footer_si['value'] - $footer_sr['value']) / ($footer_si['qty'] - $footer_sr['qty'])
                    : 0,
            ];

            $reportIndexQuery = http_build_query(array_filter([
                'from_date' => $from_date,
                'to_date' => $to_date,
                'brand' => $ctrl_brand,
                'sales_person' => $ctrl_sales_person,
                'supplier' => $ctrl_supplier,
                'company' => $ctrl_company,
            ], function ($v) {
                return $v !== '' && $v !== null;
            }));

            $r_head = SysHelper::get_data_by_role();
            $company_id_head = $r_head[0];
            $supplier_list = SysHelper::get_customer_list($company_id_head);
            $sales_person_list = SysHelper::get_staff_list();
            if (session('logged_session_data.company_id') == 1) {
                $company = SysCompany::select('id', 'company_name')->orderBy('sort_id', 'asc')->get();
            } else {
                $company = SysCompany::select('id', 'company_name')
                    ->where('id', session('logged_session_data.company_id'))
                    ->orderBy('sort_id', 'asc')
                    ->get();
            }

            return view('backEnd.inventory.InventoryBrandReportDetail', compact(
                'itemRow',
                'linesSi',
                'linesSr',
                'footer_si',
                'footer_sr',
                'footer_net',
                'from_date',
                'to_date',
                'ctrl_brand',
                'ctrl_sales_person',
                'ctrl_supplier',
                'ctrl_company',
                'reportIndexQuery',
                'supplier_list',
                'sales_person_list',
                'company'
            ));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function sales_report_detail($brand=null, $partnumber=null, $from_date=null, $to_date=null, $sales_person=null,$ctrl_supplier=null,$ctrl_company=null)
    {
        try {

            $ctrl_brand = $brand;
            $ctrl_partnumber = $partnumber;
            $ctrl_date=$from_date;
            $ctrl_date2=$to_date;
            $query = DB::table('sys_sales_invoice_items as si')
            ->select(
                's.doc_number',
                's.doc_date',
                'i.part_number as item_part_number',
                'si.part_number as item_id',
                'si.qty',
                'si.unitprice',
                'si.value',
                'si.discount',
                'si.taxableamount',
                'si.vatamount',
                'd.code as deal_code',
                's.deal_id',
                'u.full_name',
                'c.account_name',
                'invcomp.company_name as invoice_company_name',
                DB::raw("'SI' as line_kind")
            )
            ->join('sys_sales_invoice as s', 's.id', '=', 'si.si_id')
            ->leftJoin('sys_company as invcomp', 'invcomp.id', '=', 's.company_id')
            ->leftjoin('sys_crm_deals as d', 'd.id', '=', 's.deal_id')
            ->leftjoin('sm_staffs as u', 'u.user_id', '=', 's.sales_man')
            ->leftjoin('sys_chartofaccounts as c', 'c.id', '=', 's.customer')
            ->join('sm_items as i', 'i.id', '=', 'si.part_number')
            ->join('sys_brand as b', 'b.id', '=', 'i.brand');

                if (!empty($ctrl_brand) && $ctrl_brand != "all") {
                    $query->where('b.id', $ctrl_brand);
                }

                // when brand all then show all brands
                if (empty($ctrl_brand) || $ctrl_brand == "all") {
                    $query->where('b.id', '!=', 0);
                }
               
                if (!empty($ctrl_partnumber)) {
                    $query->wherein('i.part_number', $ctrl_partnumber);
                }

                if (!empty($ctrl_date)) {
                    if (!empty($ctrl_date)) {
                        $query->whereBetween(
                            DB::raw("DATE(s.doc_date)"),
                            [
                                date('Y-m-d', strtotime($ctrl_date)),
                                date('Y-m-d', strtotime($ctrl_date2))
                            ]
                        );
                    } else {
                        $query->whereDate(
                            's.doc_date',
                            date('Y-m-d', strtotime($ctrl_date))
                        );
                    }
                }
                if (!empty($ctrl_company)) {
                    if($ctrl_company != 1){
                        $query->where('s.company_id', $ctrl_company);
                    }
                }                 
                if (!empty($sales_person)) {
                    $query->where('s.sales_man', $sales_person);
                }
                if (!empty($ctrl_supplier)) {
                    $query->where('s.customer', $ctrl_supplier);
                }

            $query->where('s.status', 1);
            
            if(Auth::user()->role_id != 1){
                $query->where('s.sales_man', Auth::user()->id);
            }
            $query->orderBy('s.doc_number', 'asc')
            ->orderBy('i.part_number', 'asc')
            ->orderBy('s.doc_date', 'asc');
            $data = $query->get();

            return $data;

        } catch (\Throwable $th) {
            return $th;
        }
    }

    /**
     * Sales return line detail (same filters as sales_report_detail) for inventory brand reports.
     */
    public function sales_return_report_detail($brand = null, $partnumber = null, $from_date = null, $to_date = null, $sales_person = null, $ctrl_supplier = null, $ctrl_company = null)
    {
        try {
            $ctrl_brand = $brand;
            $ctrl_partnumber = $partnumber;
            $ctrl_date = $from_date;
            $ctrl_date2 = $to_date;
            $query = DB::table('sys_sales_return_list as srl')
                ->select(
                    'sr.doc_number',
                    'sr.doc_date',
                    'i.part_number as item_part_number',
                    'srl.part_number as item_id',
                    'srl.qty',
                    'srl.unitprice',
                    'srl.value',
                    'srl.discount',
                    'srl.taxableamount',
                    'srl.vatamount',
                    'd.code as deal_code',
                    'sr.deal_id',
                    'u.full_name',
                    'c.account_name',
                    'invcomp.company_name as invoice_company_name',
                    DB::raw("'SR' as line_kind")
                )
                ->join('sys_sales_return as sr', 'sr.id', '=', 'srl.sr_id')
                ->leftJoin('sys_company as invcomp', 'invcomp.id', '=', 'sr.company_id')
                ->leftjoin('sys_crm_deals as d', 'd.id', '=', 'sr.deal_id')
                ->leftjoin('sm_staffs as u', 'u.user_id', '=', 'sr.sales_man')
                ->leftjoin('sys_chartofaccounts as c', 'c.id', '=', 'sr.customer')
                ->join('sm_items as i', 'i.id', '=', 'srl.part_number')
                ->join('sys_brand as b', 'b.id', '=', 'i.brand');

            if (!empty($ctrl_brand) && $ctrl_brand != 'all') {
                $query->where('b.id', $ctrl_brand);
            }
            if (empty($ctrl_brand) || $ctrl_brand == 'all') {
                $query->where('b.id', '!=', 0);
            }
            if (!empty($ctrl_partnumber)) {
                $query->whereIn('i.part_number', $ctrl_partnumber);
            }
            if (!empty($ctrl_date)) {
                $query->whereBetween(
                    DB::raw('DATE(sr.doc_date)'),
                    [
                        date('Y-m-d', strtotime($ctrl_date)),
                        date('Y-m-d', strtotime($ctrl_date2)),
                    ]
                );
            }
            if (!empty($ctrl_company)) {
                if ($ctrl_company != 1) {
                    $query->where('sr.company_id', $ctrl_company);
                }
            }
            if (!empty($sales_person)) {
                $query->where('sr.sales_man', $sales_person);
            }
            if (!empty($ctrl_supplier)) {
                $query->where('sr.customer', $ctrl_supplier);
            }
            $query->where('sr.status', 1);
            if (Auth::user()->role_id != 1) {
                $query->where('sr.sales_man', Auth::user()->id);
            }
            $query->orderBy('sr.doc_number', 'asc')
                ->orderBy('i.part_number', 'asc')
                ->orderBy('sr.doc_date', 'asc');

            return $query->get();
        } catch (\Throwable $th) {
            return $th;
        }
    }

    /**
     * @param \Illuminate\Support\Collection $siLines
     * @param \Illuminate\Support\Collection $srLines
     * @return array{0: array<int, array<string, float>>, 1: array<string, float>}
     */
    protected static function inventoryBrandMergeSiSrByItem($siLines, $srLines)
    {
        $siByPart = $siLines->groupBy(function ($x) {
            return (int) $x->item_id;
        });
        $srByPart = $srLines->groupBy(function ($x) {
            return (int) $x->item_id;
        });
        $pids = $siByPart->keys()->merge($srByPart->keys())->unique()->filter();
        $byPart = [];
        foreach ($pids as $pid) {
            $pid = (int) $pid;
            $linesSi = $siByPart->get($pid, collect());
            $linesSr = $srByPart->get($pid, collect());
            $t = self::inventoryBrandEmptyPartTotals();
            foreach ($linesSi as $li) {
                self::inventoryBrandAccumulateLine($t, $li, 'si');
            }
            foreach ($linesSr as $li) {
                self::inventoryBrandAccumulateLine($t, $li, 'sr');
            }
            $t['qty'] = $t['qty_si'] - $t['qty_sr'];
            $t['value'] = $t['value_si'] - $t['value_sr'];
            $t['discount'] = $t['discount_si'] - $t['discount_sr'];
            $t['taxableamount'] = $t['taxableamount_si'] - $t['taxableamount_sr'];
            $t['vatamount'] = $t['vatamount_si'] - $t['vatamount_sr'];
            $t['total_amount'] = $t['taxableamount'] + $t['vatamount'];
            $t['avg_rate'] = $t['qty'] > 0 ? $t['value'] / $t['qty'] : 0;
            $byPart[$pid] = $t;
        }
        $grand = self::inventoryBrandEmptyPartTotals();
        foreach ($byPart as $t) {
            foreach (['qty_si', 'qty_sr', 'qty', 'value_si', 'value_sr', 'value', 'discount_si', 'discount_sr', 'discount', 'taxableamount_si', 'taxableamount_sr', 'taxableamount', 'vatamount_si', 'vatamount_sr', 'vatamount', 'total_amount'] as $k) {
                $grand[$k] += $t[$k];
            }
        }
        $grand['avg_rate_si'] = $grand['qty_si'] > 0 ? $grand['value_si'] / $grand['qty_si'] : 0;
        $grand['avg_rate_sr'] = $grand['qty_sr'] > 0 ? $grand['value_sr'] / $grand['qty_sr'] : 0;
        $grand['avg_rate'] = $grand['qty'] > 0 ? $grand['value'] / $grand['qty'] : 0;

        return [$byPart, $grand];
    }

    protected static function inventoryBrandEmptyPartTotals()
    {
        return [
            'qty_si' => 0.0,
            'qty_sr' => 0.0,
            'qty' => 0.0,
            'value_si' => 0.0,
            'value_sr' => 0.0,
            'value' => 0.0,
            'discount_si' => 0.0,
            'discount_sr' => 0.0,
            'discount' => 0.0,
            'taxableamount_si' => 0.0,
            'taxableamount_sr' => 0.0,
            'taxableamount' => 0.0,
            'vatamount_si' => 0.0,
            'vatamount_sr' => 0.0,
            'vatamount' => 0.0,
            'total_amount' => 0.0,
            'avg_rate_si' => 0.0,
            'avg_rate_sr' => 0.0,
            'avg_rate' => 0.0,
        ];
    }

    protected static function inventoryBrandAccumulateLine(array &$bucket, $li, $kind)
    {
        $qty = (float) $li->qty;
        $value = (float) $li->value;
        $discount = (float) $li->discount;
        $tax = (float) $li->taxableamount;
        $vat = (float) $li->vatamount;
        if ($kind === 'si') {
            $bucket['qty_si'] += $qty;
            $bucket['value_si'] += $value;
            $bucket['discount_si'] += $discount;
            $bucket['taxableamount_si'] += $tax;
            $bucket['vatamount_si'] += $vat;
        } else {
            $bucket['qty_sr'] += $qty;
            $bucket['value_sr'] += $value;
            $bucket['discount_sr'] += $discount;
            $bucket['taxableamount_sr'] += $tax;
            $bucket['vatamount_sr'] += $vat;
        }
    }

}
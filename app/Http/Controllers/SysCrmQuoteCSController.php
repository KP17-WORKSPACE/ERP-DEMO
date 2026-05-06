<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SmInspectingDepartment;
use App\SmItem;
use App\SmItemCategory;
use Illuminate\Http\Request;
use App\SmItemStore;
use App\SmItemSubcategory;
use App\SmStaff;
use App\SysBrand;
use App\SysChartofAccounts;
use App\SysCompany;
use App\SysCountries;
use App\SysCrmDeals;
use App\SysCrmDealsComments;
use App\SysCrmQuoteCart;
use App\SysCrmQuoteCSItems;
use App\SysCrmQuoteItems;
use App\SysCurrencySettings;
use App\SysCustSuppl;
use App\SysItemOpeningStock;
use App\SysItemStock;
use App\SysPaymentTerms;
use App\SysPriceBook;
use App\SysShipping;
use App\SysStockIn;
use App\SysStockInSerialNo;
use App\SysSupplierType;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;
use Validator;
use Barryvdh\DomPDF\Facade as PDF;

class SysCrmQuoteCSController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    
    public function quoteadd(Request $request, $id)
    {
        try {
            $quotation = SysCrmDeals::where('id',$id)->first();
            $quotationitems = SysCrmQuoteCSItems::where('deal_id',$id)->orderby('id','ASC')->get();
            $paymenttermslist = SysPaymentTerms::get();

        if(count($quotationitems)>0){
            $deal_id            =   $quotationitems[0]->deal_id;
            $company_id         =   $quotationitems[0]->company_id;
            $currency_id        =   $quotationitems[0]->currency_id;
            $customer_type      =   $quotationitems[0]->customer_type;
            $currency_code      =   $quotationitems[0]->currency->code;
            $payment_terms      =   $quotationitems[0]->payment_terms;
            $payment_terms_name =   $quotationitems[0]->paymentterms->title;
            $delivery_date      =   $quotationitems[0]->delivery_date;
        }
        else if(session('form_session_data.company_id'))
        {
            $deal_id            =   $id;
            $company_id         =   session('form_session_data.company_id');
            $currency_id        =   session('form_session_data.currency_id');
            $customer_type      =   session('form_session_data.customer_type');
            $currency_code      =   "";
            $payment_terms      =   session('form_session_data.payment_terms');
            $payment_terms_name =   "";
            $delivery_date      =   session('form_session_data.delivery_date');
        }
        else
        {
            return redirect('crm-deals/'.$id.'/view');            
        }
        
        if($_POST){
            try {
                $flag = SysCrmQuoteCSItems::where([

                    ['user_id',Auth::user()->id],
                    ['deal_id',$deal_id],
                    ['company_id',$company_id],
                    ['currency_id',$currency_id],
                    ['customer_type',$customer_type],
                    ['payment_terms',$payment_terms],
                    ['delivery_date',$delivery_date],
                    ['description',$request->description],
                    ['work_stations',$request->work_stations],
                    ['price_per_month',$request->price_per_month],
                    ['critical_assets',$request->critical_assets],
                    ['additional_critical_assets',$request->additional_critical_assets],
                    ['price_per_critical_asset',$request->price_per_critical_asset],
                    ['total_price_per_month',$request->total_price_per_month],
                    ['created_by',Auth::user()->id],
                    ['company_id',session('logged_session_data.company_id')]
                    ])->first();
                if($flag){

                    }
                else{
                    DB::table('sys_crm_quote_cs_items')->insert(
                        [
                            'user_id' => Auth::user()->id,
                            'deal_id' => $deal_id,
                            'company_id' => $company_id,
                            'currency_id' => $currency_id,
                            'customer_type' => $customer_type,
                            'payment_terms' => $payment_terms,
                            'delivery_date' => $delivery_date,
                            'description' => $request->description,
                            'work_stations' => $request->work_stations,
                            'price_per_month' => $request->price_per_month,
                            'critical_assets' => $request->critical_assets,
                            'additional_critical_assets' => $request->additional_critical_assets,
                            'price_per_critical_asset' => $request->price_per_critical_asset,
                            'total_price_per_month' => $request->total_price_per_month,
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                        ]
                        );
                }
                
            $quotation = SysCrmDeals::where('id',$id)->first();
            $quotationitems = SysCrmQuoteCSItems::where('deal_id',$id)->orderby('id','ASC')->get();
            } catch (\Throwable $th) {
                return $th;
            }
        }

        return view('backEnd.crm.QuoteCS',compact('quotation','quotationitems','deal_id','company_id','currency_id','customer_type','currency_code','payment_terms','delivery_date','payment_terms_name','paymenttermslist'));

        } catch (\Throwable $th) {
            return $th;
            return redirect('crm-deals/'.$id.'/view');
        }
    }
    
    public function deleteitems(Request $request)
    {
        $input = $request->all();
        
        try{        
            DB::table('sys_crm_quote_cs_items')->where('id', $request->id)->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            return $e;
            $bug = $e->errorInfo[1];
        }
        if($bug==0){
            $retData = "OK";
            return json_encode(array('data'=>$retData));
        }else {
            $retData='ERROR';
            return json_encode(array('data'=>$retData));
        }
    }
    
    public function download(Request $request, $id)
    {
        try {
        $quotation = SysCrmDeals::where('id',$id)->first();
        $quotationitems = SysCrmQuoteCSItems::where('deal_id',$id)->orderby('id','ASC')->get();

        $currency=$quotationitems[0]->currency->code;
        $paymentterms=$quotationitems[0]->paymentterms->title;
        $deliverydate=$quotationitems[0]->delivery_date;
        
        $pdfheader = $quotationitems[0]->company->pdf_header;
        $pdffooter = $quotationitems[0]->company->pdf_footer;
        $pdfwatermark = $quotationitems[0]->company->pdf_watermark;
        $pdffirstpage = $quotationitems[0]->company->pdf_first_page;
        $net_vat = $quotationitems[0]->company->net_vat;
        if($net_vat==""){$net_vat=5;}

        $data = [
            'quotation'   => $quotation,
            'quotationitems'      => $quotationitems,
            'currency'      => $currency,
        ];
        //return $data;
        $wp = $request->wp;

        $pdf = PDF::loadView('backEnd.crm.QuoteCSPDF',['quotation' => $quotation, 'quotationitems'=>$quotationitems, 'currency'=>$currency, 'paymentterms'=>$paymentterms, 'deliverydate'=>$deliverydate, 'wp'=>$wp, 'pdfheader'=>$pdfheader, 'pdffooter'=>$pdffooter, 'pdfwatermark'=>$pdfwatermark, 'pdffirstpage'=>$pdffirstpage, 'net_vat'=>$net_vat]);
        $pdf->setPaper('A4', 'portrait');
        $pageName = "Quote-No-".$id.".pdf";
        return $pdf->download($pageName);

        //return view('admin.boq.form_pdf',['boq' => $boq, 'items'=>$items]);
            
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function chooseitems(Request $request)
    {
        try {

            $brands = SysBrand::all();
            $itemCategories = SmItemCategory::all();
            $SuCategories = SmItemSubcategory::all();
            
        $cart_items = SysCrmQuoteCart::select('sys_crm_quote_cart.id','sys_crm_quote_cart.qty','sys_crm_quote_cart.price','sm_items.part_number','sys_crm_quote_cart.description')
        ->join('sm_items','sm_items.id','sys_crm_quote_cart.product_id')
        ->where(['cart_id' => session('logged_session_data.cart_id')])->get();

        $currancy = DB::table('sys_currency')->select('code')->where('id',session('form_session_data.currency_id'))->first();
        
        $product = [];
        if($_POST){
            try {
                if(session('form_session_data.customer_type')==1){

                    $product = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.r_price as price')
                    ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                    ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->where(function($query) use ($request) {
                                $query->where('sm_items.part_number','like','%'.$request->part_number.'%')
                                            ->orwhere('sm_items.description','like','%'.$request->part_number.'%');
                    })->get();

                    // $product = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.r_price as price')
                    // ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                    // ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))
                    // ->where('sm_items.part_number','like','%'.$request->part_number.'%')
                    // ->orwhere('sm_items.description','like','%'.$request->part_number.'%')->get();
                }
                else{
                    $product = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.e_price as price')
                    ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                    ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->where(function($query) use ($request) {
                                $query->where('sm_items.part_number','like','%'.$request->part_number.'%')
                                            ->orwhere('sm_items.description','like','%'.$request->part_number.'%');
                    })->get();

                    // $product = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.e_price as price')
                    // ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                    // ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))
                    // ->where('sm_items.part_number','like','%'.$request->part_number.'%')
                    // ->orwhere('sm_items.description','like','%'.$request->part_number.'%')->get();
                }
            } catch (\Throwable $th) {
                return $th;
            }
        }
        return view('backEnd.crm.QuoteNew',compact('product','cart_items','currancy','brands','itemCategories','SuCategories'));
    } catch (\Throwable $th) {
        return $th;
    }
    }
    public function additems(Request $request)
    {
            try {
                 $cart=SysCrmQuoteCart::select('id','qty')
                 ->where(['cart_id' => session('logged_session_data.cart_id'),'product_id'=> $request->id])->first();

                 if(isset($cart)){
                     DB::table('sys_crm_quote_cart')->where('id',$cart->id)
                         ->update([
                             'qty' => $cart->qty + $request->qty,
                             'price' => $request->price,
                             'description' => $request->description,
                             'updated_by' => Auth::user()->id,
                             'updated_at' => date('Y-m-d H:i:s'),
                         ]);
                 }
                 else{
                    DB::table('sys_crm_quote_cart')->insert(
                        [
                            'cart_id' => session('logged_session_data.cart_id'),
                            'user_id' => Auth::user()->id,
                            'deal_id' => session('form_session_data.deal_id'),
                            'company_id' => session('form_session_data.company_id'),
                            'currency_id' => session('form_session_data.currency_id'),
                            'customer_type' => session('form_session_data.customer_type'),
                            'payment_terms' => session('form_session_data.payment_terms'),
                            'delivery_date' => session('form_session_data.delivery_date'),
                            'product_id' => $request->id,
                            'qty' => $request->qty,
                            'price' => $request->price,
                            'description' => $request->description,
                            'discount' => 0,
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                        ]
                        );
                 }
            $bug = 0;
            } catch (\Throwable $e) {
                return $e;
                $bug = $e->errorInfo[1];
            }
            if($bug==0){
                $retData = "OK";
                return json_encode(array('data'=>$retData));
            }else {
                $retData='ERROR';
                return json_encode(array('data'=>$retData));
            }
    }
    public function addbulkitems(Request $request)
    {
        $add_check = $request['add'];
        if(array_sum($add_check)==0)
        {
            Toastr::error('Product Added Failed, Please Select Products', 'Failed');
            return redirect()->back();
        }
		foreach($add_check AS $dat) { if($dat === '1'){ if(end($add_id)==0){ array_pop($add_id); } $add_id[]=1; } else{ $add_id[]=0; } }

            try {
                for ($i=0; $i < count($add_id); $i++) {
                    if($add_id[$i]==1){
                        $qty = $request->b_qty[$i];
    
                        if($request->b_qty[$i]=="" || $request->b_qty[$i]==0){
                            $qty=1;
                        }
                        $cart=SysCrmQuoteCart::select('id','qty')
                        ->where(['cart_id' => session('logged_session_data.cart_id'),'product_id'=> $request->pid[$i]])->first();

                        if(isset($cart)){
                            DB::table('sys_crm_quote_cart')->where('id',$cart->id)
                                ->update([
                                    'qty' => $cart->qty + $qty,
                                    'price' => $request->b_price[$i],
                                    'description' => $request->b_description[$i],
                                    'updated_by' => Auth::user()->id,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }
                        else{
                           DB::table('sys_crm_quote_cart')->insert(
                               [
                                   'cart_id' => session('logged_session_data.cart_id'),
                                   'user_id' => Auth::user()->id,
                                   'deal_id' => session('form_session_data.deal_id'),
                                   'company_id' => session('form_session_data.company_id'),
                                   'currency_id' => session('form_session_data.currency_id'),
                                   'customer_type' => session('form_session_data.customer_type'),
                                   'payment_terms' => session('form_session_data.payment_terms'),
                                   'delivery_date' => session('form_session_data.delivery_date'),
                                   'product_id' => $request->pid[$i],
                                   'qty' => $qty,
                                   'price' => $request->b_price[$i],
                                   'description' => $request->b_description[$i],
                                   'discount' => 0,
                                   'status' => 1,
                                   'created_by' => Auth::user()->id,
                               ]
                               );
                        }

                    }
                }

            $bug = 0;
            } catch (\Throwable $e) {
                return $e;
                $bug = $e->errorInfo[1];
            }
            if($bug==0){
			    Toastr::success('Product Added Successfully', 'Success');
                return redirect()->back(); 
            }else {
                Toastr::error('Product Added Failed', 'Failed');
                return redirect()->back(); 
            }
    }

    public function generatequote(Request $request)
    {
        $cart=SysCrmQuoteCart::where(['cart_id' => session('logged_session_data.cart_id'),'user_id'=> Auth::user()->id])->get();
        foreach($cart as $items)
        {
            DB::table('sys_crm_quote_items')->insert([
                'user_id' => $items->user_id,
                'deal_id' => $items->deal_id,
                'company_id' => $items->company_id,
                'currency_id' => $items->currency_id,
                'customer_type' => $items->customer_type,
                'payment_terms' => $items->payment_terms,
                'delivery_date' => $items->delivery_date,
                'product_id' => $items->product_id,
                'qty' => $items->qty,
                'price' => $items->price,
                'description' => $items->description,
                'discount' => $items->discount,
                'status' => $items->status,
                'created_by' => Auth::user()->id,
            ]);
        }
        
        DB::table('sys_crm_quote_cart')->where(['cart_id' => session('logged_session_data.cart_id'),'user_id'=> Auth::user()->id])->delete();
        
        return redirect('crm-deals/'.$items->deal_id.'/view');

    }
    

    public function quoteaddnew(Request $request, $id)
    {
        try {
        $quotation = SysCrmDeals::where('id',$id)->first();
        $quotationitems = SysCrmQuoteItems::where('deal_id',$id)->orderby('id','ASC')->get();

        $deal_id        =   $quotationitems[0]->deal_id;
        $company_id     =   $quotationitems[0]->company_id;
        $currency_id    =   $quotationitems[0]->currency_id;
        $customer_type  =   $quotationitems[0]->customer_type;
        $currency_code  =   $quotationitems[0]->currency->code;
        $payment_terms  =   $quotationitems[0]->payment_terms;
        $payment_terms_name  =   $quotationitems[0]->paymentterms->title;
        $delivery_date  =   $quotationitems[0]->delivery_date;
        
        $brands = SysBrand::all();
        $itemCategories = SmItemCategory::all();
        $SuCategories = SmItemSubcategory::all();
        
        $product = [];
        if($_POST){
            try {
                if($customer_type==1){                    
                    $product = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.r_price as price')
                    ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                    ->where('sys_price_book.currency_id', $currency_id)->where(function($query) use ($request) {
                                $query->where('sm_items.part_number','like','%'.$request->part_number.'%')
                                            ->orwhere('sm_items.description','like','%'.$request->part_number.'%');
                    })->get();

                    // $product = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.r_price as price')
                    // ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                    // ->where('sys_price_book.currency_id', $currency_id)
                    // ->where('sm_items.part_number','like','%'.$request->part_number.'%')
                    // ->orwhere('sm_items.description','like','%'.$request->part_number.'%')->get();
                }
                else{
                    $product = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.e_price as price')
                    ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                    ->where('sys_price_book.currency_id', $currency_id)->where(function($query) use ($request) {
                                $query->where('sm_items.part_number','like','%'.$request->part_number.'%')
                                            ->orwhere('sm_items.description','like','%'.$request->part_number.'%');
                    })->get();

                    // $product = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.e_price as price')
                    // ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                    // ->where('sys_price_book.currency_id', $currency_id)
                    // ->where('sm_items.part_number','like','%'.$request->part_number.'%')
                    // ->orwhere('sm_items.description','like','%'.$request->part_number.'%')->get();
                }
            } catch (\Throwable $th) {
                return $th;
            }
        }

        return view('backEnd.crm.QuoteEditNew',compact('quotation','quotationitems','deal_id','company_id','currency_id','customer_type','currency_code','product','payment_terms','delivery_date','payment_terms_name','brands','itemCategories','SuCategories'));

        } catch (\Throwable $th) {
            return redirect('crm-deals/'.$id.'/view');
        }
    }

    public function additemsedit(Request $request)
    {
        try {
            $quote=SysCrmQuoteItems::select('id','qty')
            ->where(['deal_id' => $request->deal_id,'product_id'=> $request->id])->first();

            if(isset($quote)){
                DB::table('sys_crm_quote_items')->where('id',$quote->id)
                    ->update([
                        'qty' => $quote->qty + $request->qty,
                        'price' => $request->price,
                        'description' => $request->description,
                        'updated_by' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }
            else{
            DB::table('sys_crm_quote_items')->insert(
                [
                    'user_id' => Auth::user()->id,
                    'deal_id' => $request->deal_id,
                    'company_id' => $request->company_id,
                    'currency_id' => $request->currency_id,
                    'customer_type' => $request->customer_type,
                    'payment_terms' => $request->payment_terms,
                    'delivery_date' => $request->delivery_date,
                    'product_id' => $request->id,
                    'qty' => $request->qty,
                    'price' => $request->price,
                    'description' => $request->description,
                    'discount' => 0,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                ]
                );
            }
        $bug = 0;            
        } catch (\Throwable $e) {
            return $e;
            $bug = $e->errorInfo[1];
        }
        if($bug==0){
            $retData = "OK";
            return json_encode(array('data'=>$retData));
        }else {
            $retData='ERROR';
            return json_encode(array('data'=>$retData));
        }
    }
    public function addbulkitemsedit(Request $request)
    {
		$add_check = $request['add'];
        if(array_sum($add_check)==0)
        {
            Toastr::error('Product Added Failed, Please Select Products', 'Failed');
            return redirect()->back();
        }
		foreach($add_check AS $dat) { if($dat === '1'){ if(end($add_id)==0){ array_pop($add_id); } $add_id[]=1; } else{ $add_id[]=0; } }
        
        try {
            for ($i=0; $i < count($add_id); $i++) {
                if($add_id[$i]==1){
                    $qty = $request->b_qty[$i];

                    if($request->b_qty[$i]=="" || $request->b_qty[$i]==0){
                        $qty=1;
                    }
                    $quote=SysCrmQuoteItems::select('id','qty')
                    ->where(['deal_id' => $request->b_deal_id,'product_id'=> $request->pid[$i]])->first();
                    if(isset($quote)){
                        DB::table('sys_crm_quote_items')->where('id',$quote->id)
                            ->update([
                                'qty' => $quote->qty + $qty,
                                'price' => $request->b_price[$i],
                                'description' => $request->b_description[$i],
                                'updated_by' => Auth::user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    }
                    else{
                        DB::table('sys_crm_quote_items')->insert(
                            [
                                'user_id' => Auth::user()->id,
                                'deal_id' => $request->b_deal_id,
                                'company_id' => $request->b_company_id,
                                'currency_id' => $request->b_currency_id,
                                'customer_type' => $request->b_customer_type,
                                'payment_terms' => $request->b_payment_terms,
                                'delivery_date' => $request->b_delivery_date,
                                'product_id' => $request->pid[$i],
                                'qty' => $qty,
                                'price' => $request->b_price[$i],
                                'description' => $request->b_description[$i],
                                'discount' => 0,
                                'status' => 1,
                                'created_by' => Auth::user()->id,
                            ]
                            );
                        }
                }
            }
        $bug = 0;            
        } catch (\Throwable $e) {
            return $e;
            $bug = $e->errorInfo[1];
        }
        if($bug==0){
			Toastr::success('Product Added Successfully', 'Success');
            return redirect()->back(); 
        }else {
            Toastr::error('Product Added Failed', 'Failed');
            return redirect()->back(); 
        }
    }

    public function addnewbulkitemsedit(Request $request)
    {
		$add_check = $request['add'];
        if(array_sum($add_check)==0)
        {
            Toastr::error('Product Added Failed, Please Select Products', 'Failed');
            return redirect()->back();
        }
		foreach($add_check AS $dat) { if($dat === '1'){ if(end($add_id)==0){ array_pop($add_id); } $add_id[]=1; } else{ $add_id[]=0; } }
        
        try {
            for ($i=0; $i < count($add_id); $i++) {
                if($add_id[$i]==1){
                    $qty = $request->b_qty[$i];

                    if($request->b_qty[$i]=="" || $request->b_qty[$i]==0){
                        $qty=1;
                    }
                    $quote=SysCrmQuoteItems::select('id','qty')
                    ->where(['deal_id' => $request->b_deal_id,'product_id'=> $request->pid[$i]])->first();
                    if(isset($quote)){
                        DB::table('sys_crm_quote_items')->where('id',$quote->id)
                            ->update([
                                'qty' => $quote->qty + $qty,
                                'price' => $request->b_price[$i],
                                'description' => $request->b_description[$i],
                                'updated_by' => Auth::user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    }
                    else{
                        DB::table('sys_crm_quote_items')->insert(
                            [
                                'user_id' => Auth::user()->id,
                                'deal_id' => $request->b_deal_id,
                                'company_id' => $request->b_company_id,
                                'currency_id' => $request->b_currency_id,
                                'customer_type' => $request->b_customer_type,
                                'payment_terms' => $request->b_payment_terms,
                                'delivery_date' => $request->b_delivery_date,
                                'product_id' => $request->pid[$i],
                                'qty' => $qty,
                                'price' => $request->b_price[$i],
                                'description' => $request->b_description[$i],
                                'discount' => 0,
                                'status' => 1,
                                'created_by' => Auth::user()->id,
                            ]
                            );
                        }
                }
            }
        $bug = 0;            
        } catch (\Throwable $e) {
            return $e;
            $bug = $e->errorInfo[1];
        }
        if($bug==0){
			Toastr::success('Product Added Successfully', 'Success');
            return redirect()->back(); 
        }else {
            Toastr::error('Product Added Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function upditemsedit(Request $request)
    {
        try {
            $quote=SysCrmQuoteItems::select('id','qty')
            ->where(['deal_id' => $request->deal_id,'id'=> $request->id])->first();
            if(isset($quote)){
                DB::table('sys_crm_quote_items')->where('id',$quote->id)
                    ->update([
                        'qty' => $request->qty,
                        'price' => $request->price,
                        'description' => $request->description,
                        'discount' => $request->discount,
                        'updated_by' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }
            $bug=0;
        } catch (\Throwable $e) {
            return json_encode(array('data'=>$e));
            $bug = $e->errorInfo[1];
        }
        if($bug==0){
            $retData = "OK";
            return json_encode(array('data'=>$retData));
        }else {
            $retData='ERROR';
            return json_encode(array('data'=>$retData));
        }
    }
    public function deleteitemsedit(Request $request)
    {
        $input = $request->all();
        
        try{        
            DB::table('sys_crm_quote_items')->where('id', $request->id)->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            return $e;
            $bug = $e->errorInfo[1];
        }
        if($bug==0){
            $retData = "OK";
            return json_encode(array('data'=>$retData));
        }else {
            $retData='ERROR';
            return json_encode(array('data'=>$retData));
        }
    }
    
    public function addnewproduct(Request $request)
    {
        $check_part_number = SmItem::select('id')->where('part_number', $request->part_number)->get();
        if(count($check_part_number)>0){
            Toastr::error('Part Number Already Existing, please check try again', 'Failed');
            return redirect()->back();
            //return redirect()->back()->with('message-danger', 'Part Number Already Existing, please check try again');
        }
        try {
            $brand = $request->brand;
            $category_name = $request->category_name;
            $subcategory_name = $request->subcategory_name;
            if($request->brand==""){$brand = 13;} //Other Brand
            if($request->category_name==""){$category_name = 14;} //Other Category
            if($request->subcategory_name==""){$subcategory_name = 78;} //Other Sub Category

            $items = new SmItem();
            //$items->item_name = $request->item_name;
            $items->item_code = $request->part_number;
            $items->part_number = $request->part_number;
            $items->brand = $brand;
            $items->product_type = 1;
            $items->category_name = $category_name;
            $items->subcategory_name = $subcategory_name;
            $items->description = $request->description;
            $items->vat = "5";
            $items->uom = "5";
            $items->coo = "5";
            $items->hscode = "5";
            $items->weight = "0.00";
            $items->status = 1;
            $items->created_by = Auth::user()->id;
            $results = $items->save();

            SysPriceBook::insert([
                'pid' => $items->id,
                'currency_id' => 1,
                'r_price' => '0.00',
                'e_price' => '0.00',
                'status' => 1,
                'created_by' => Auth::user()->id,
                'company_id' => session('logged_session_data.company_id')
            ]);
            SysPriceBook::insert([
                'pid' => $items->id,
                'currency_id' => 2,
                'r_price' => '0.00',
                'e_price' => '0.00',
                'status' => 1,
                'created_by' => Auth::user()->id,
                'company_id' => session('logged_session_data.company_id')
            ]);
            SysPriceBook::insert([
                'pid' => $items->id,
                'currency_id' => 3,
                'r_price' => '0.00',
                'e_price' => '0.00',
                'status' => 1,
                'created_by' => Auth::user()->id,
                'company_id' => session('logged_session_data.company_id')
            ]);
            SysPriceBook::insert([
                'pid' => $items->id,
                'currency_id' => 4,
                'r_price' => '0.00',
                'e_price' => '0.00',
                'status' => 1,
                'created_by' => Auth::user()->id,
                'company_id' => session('logged_session_data.company_id')
            ]);
            SysPriceBook::insert([
                'pid' => $items->id,
                'currency_id' => 5,
                'r_price' => '0.00',
                'e_price' => '0.00',
                'status' => 1,
                'created_by' => Auth::user()->id,
                'company_id' => session('logged_session_data.company_id')
            ]);
            
			Toastr::success('Product Added Successfully', 'Success');
            return redirect()->back(); 

        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Product Added Failed', 'Failed');
            return redirect()->back();
        }

    }

    public function store(Request $request)
    {
        $tags = "";
        if($request->tags!="") { $tags =implode(",",$request->tags); }
        $doc_file = "";
        if ($request->file('doc') != "") { 
            $file = $request->file('doc');
            $doc_file = md5(time()) . "." . $file->getclientoriginalextension();
            $file->move('public/uploads/crm_lead_doc/', $doc_file);
            $doc_file = $doc_file;
        }

        DB::beginTransaction();
        try {
           $scd = new SysCrmDeals();
           $scd->date = date('Y-m-d', strtotime($request->date));
           $scd->deal_name = $request->deal_name;
           $scd->cust_id = $request->cust_id;
           $scd->cust_name = $request->cust_name;
           $scd->cust_no = $request->cust_no;
           $scd->cust_email = $request->cust_email;
           $scd->deal_value = $request->deal_value;
           $scd->source = $request->source;
           $scd->source_o = $request->source_o;
           $scd->tags = $tags;
           $scd->stage = $request->stage;
           $scd->owner = $request->owner;
           $scd->doc = $doc_file;
           $scd->estimated_close_date = date('Y-m-d', strtotime($request->estimated_close_date));
           $scd->created_by = Auth::user()->id;
           $scd->company_id = session('logged_session_data.company_id');
           $scd->save();
           $scd->toArray();

           $results=0;
           DB::commit();
           
        if ($results==0) {
            Toastr::success('Deal has been added successfully', 'Success');
            return redirect()->back();
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
    
    public function show()
    {
        try{
            $deals = SysCrmDeals::all();

            return view('backEnd.crm.DealList', compact('deals'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           //return redirect()->back();
           return $e;
        }
    }
    
    public function edit(Request $request, $id)
    {
        try{
            $currency       = SysCurrencySettings::select('id','code')->get();
            $company        = SysCompany::find(session('logged_session_data.company_id'));
            $staff      = SmStaff::select('user_id','full_name')->get();
            $vendors        = SysCustSuppl::select('id','code','name')->where('catid',1)->get(); // 1 customers, 2 suppliers
            $deals = SysCrmDeals::all();
            $brand = SysBrand::all();
            $edit = SysCrmDeals::where('id',$id)->first();
            $country = SysCountries::select('id','name')->get();
            return view('backEnd.crm.DealForm', compact('currency', 'vendors', 'company','staff','edit','deals','brand','country'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function view($id)
    {
        try{
            $currency       = SysCurrencySettings::select('id','code')->get();
            $company        = SysCompany::find(session('logged_session_data.company_id'));
            $staff      = SmStaff::select('user_id','full_name')->get();
            $vendors        = SysCustSuppl::select('id','code','name')->where('catid',1)->get(); // 1 customers, 2 suppliers
            $leads = SysCrmDeals::where('id',$id)->first();
            $edit = SysCrmDeals::where('id',$id)->first();
            $comments = SysCrmDealsComments::where('deal_id',$id)->orderBy('id','DESC')->get();

            return view('backEnd.crm.DealView', compact('currency', 'vendors', 'company','staff','edit','leads','comments'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function update(Request $request, $id)
    {
        $tags = "";
        if($request->tags!="") { $tags =implode(",",$request->tags); }
        $doc_file = $request->file_name;
        if ($request->file('doc') != "") { 
            $file = $request->file('doc');
            $doc_file = md5(time()) . "." . $file->getclientoriginalextension();
            $file->move('public/uploads/crm_lead_doc/', $doc_file);
            $doc_file = $doc_file;
        }

        $scd = SysCrmDeals::find($id);
           $scd->date = date('Y-m-d', strtotime($request->date));
           $scd->deal_name = $request->deal_name;
           $scd->cust_id = $request->cust_id;
           $scd->cust_name = $request->cust_name;
           $scd->cust_no = $request->cust_no;
           $scd->cust_email = $request->cust_email;
           $scd->deal_value = $request->deal_value;
           $scd->source = $request->source;
           $scd->source_o = $request->source_o;
           $scd->tags = $tags;
           $scd->stage = $request->stage;
           $scd->owner = $request->owner;
           $scd->doc = $doc_file;
           $scd->estimated_close_date = date('Y-m-d', strtotime($request->estimated_close_date));
           $scd->updated_by = Auth::user()->id;
           $scd->company_id = session('logged_session_data.company_id');
           $results = $scd->update();

        if ($results) {
            Toastr::success('Deal has been updated successfully', 'Success');
            return redirect()->back();
        } else {
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function crmdealscommentsadd(Request $request)
    {
        try {
            DB::table('sys_crm_deals_comments')->insert(
                [
                    'deal_id' => $request->commentsid,
                    'comments' => $request->comments,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                ]
                );
            Toastr::success('Comments has been added successfully', 'Success');
            return redirect()->back();

        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function updatepaymentterms(Request $request)
    {
        try {
            DB::table('sys_crm_quote_items')->where('deal_id',$request->edit_deal_id)->update(
                [
                    'payment_terms' => $request->payment_terms,
                    'delivery_date' => date('Y-m-d', strtotime($request->delivery_date)),
                    'updated_by' => Auth::user()->id,
                ]
                );
            Toastr::success('Updated successfully', 'Success');
            return redirect()->back();

        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        //
    }

    public function deleteStoreView(Request $request,$id){

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($id, null);
        }
         return view('backEnd.inventory.deleteItemStoreView', compact('id'));
    }

    public function deleteStore(Request $request,$id){
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
}

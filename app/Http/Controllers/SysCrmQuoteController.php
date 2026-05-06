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
use App\SysCrmQuoteItems;
use App\SysCurrencySettings;
use App\SysCustSuppl;
use App\SysHelper;
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
use App\SysCurrency;
use App\SysCrmEndUser;
use App\SysCrmSupport;
use App\SysCrmDealsCollaboration;
use App\SysCrmService;

class SysCrmQuoteController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    
    public function index(Request $request)
    {
        $brands = SysBrand::all();
        $itemCategories = SmItemCategory::all();
        $SuCategories = SmItemSubcategory::all();

        if($_POST){
            $form_data = [
                "company_id"    => $request->company_id,
                "currency_id"   => $request->currency_id,
                "customer_type" => $request->customer_type,
                "deal_id"       => $request->deal_id,
                "payment_terms" => $request->payment_terms,
                "payment_terms_txt" => $request->payment_terms_txt,
                "delivery_time" => $request->delivery_time,
                "delivery_date" => date('Y-m-d', strtotime($request->delivery_date)),
            ];

            DB::table('sys_crm_deals')->where('id',$request->deal_id)
            ->update([
                'terms_and_condition' => $request->terms_and_condition,
                'quote_validity' => $request->quote_validity,
                //'estimated_close_date' => date('Y-m-d', strtotime($request->delivery_date)),
            ]);


            session()->put('form_session_data', $form_data);
            
            if($request->submit=='CS'){
                return redirect('crm-quote-cs/'.$request->deal_id.'/add'); 
            }
            
            return view('backEnd.crm.QuoteNew',compact('brands','itemCategories','SuCategories'));
        }
        else{
            return view('backEnd.crm.QuoteNew',compact('brands','itemCategories','SuCategories'));
        }
    }
    
    public function searchitems(Request $request)
    {
        try {

            $brands = SysBrand::all();
            $itemCategories = SmItemCategory::all();
            $SuCategories = SmItemSubcategory::all();
            
        $cart_items = SysCrmQuoteCart::select('sys_crm_quote_cart.id','sys_crm_quote_cart.qty','sys_crm_quote_cart.price','sm_items.part_number','sys_crm_quote_cart.description','sys_crm_quote_cart.discount')
        ->join('sm_items','sm_items.id','sys_crm_quote_cart.product_id')
        ->where(['cart_id' => session('logged_session_data.cart_id')])->orderby('sort_id','ASC')->get();

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
        return view('backEnd.crm.QuoteNewAdd',compact('product','cart_items','currancy','brands','itemCategories','SuCategories'));
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
            
        $cart_items = SysCrmQuoteCart::select('sys_crm_quote_cart.id','sys_crm_quote_cart.qty','sys_crm_quote_cart.price','sm_items.part_number','sys_crm_quote_cart.description','sys_crm_quote_cart.discount')
        ->join('sm_items','sm_items.id','sys_crm_quote_cart.product_id')
        ->where(['cart_id' => session('logged_session_data.cart_id')])->orderby('sort_id','ASC')->get();

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
                
                $quotationitems = SysCrmQuoteCart::where('cart_id',session('logged_session_data.cart_id'))
                 ->where('deal_id',session('form_session_data.deal_id'))
                 ->where('user_id',Auth::user()->id)->get();
                if(count($quotationitems)>0){
                    for($i = 0; $i < count($quotationitems); $i++)
                    {
                        DB::table('sys_crm_quote_cart')->where('id',$quotationitems[$i]->id)
                                ->update([ 'sort_id' => $i+1,]);
                    }
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
                 //$cart=SysCrmQuoteCart::select('id','qty')
                 //->where(['cart_id' => session('logged_session_data.cart_id'),'product_id'=> $request->id,'description' => $request->description])->first();

                //  if(isset($cart)){
                //      DB::table('sys_crm_quote_cart')->where('id',$cart->id)
                //          ->update([
                //              'qty' => $cart->qty + $request->qty,
                //              'price' => $request->price,
                //              'description' => $request->description,
                //              'updated_by' => Auth::user()->id,
                //              'updated_at' => date('Y-m-d H:i:s'),
                //          ]);
                //  }
                //  else{
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
                            'payment_terms_txt' => session('form_session_data.payment_terms_txt'),
                            'delivery_time' => session('form_session_data.delivery_time'),
                            'product_id' => $request->id,
                            'qty' => $request->qty,
                            'price' => $request->price,
                            'description' => $request->description,
                            'discount' => 0,
                            'status' => 1,
                            'sort_id' => 999,
                            'created_by' => Auth::user()->id,
                        ]
                        );
                 //}                 

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
    public function updateitems(Request $request)
    {
            try {
                     DB::table('sys_crm_quote_cart')->where('id',$request->id)
                         ->update([
                             'qty' => $request->qty,
                             'price' => $request->price,
                             'description' => $request->description,
                             'discount' => $request->discount,
                             'updated_by' => Auth::user()->id,
                             'updated_at' => date('Y-m-d H:i:s'),
                         ]);
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
                        //$cart=SysCrmQuoteCart::select('id','qty')
                        //->where(['cart_id' => session('logged_session_data.cart_id'),'product_id'=> $request->pid[$i],'description' => $request->b_description[$i]])->first();

                        // if(isset($cart)){
                        //     DB::table('sys_crm_quote_cart')->where('id',$cart->id)
                        //         ->update([
                        //             'qty' => $cart->qty + $qty,
                        //             'price' => $request->b_price[$i],
                        //             'description' => $request->b_description[$i],
                        //             'updated_by' => Auth::user()->id,
                        //             'updated_at' => date('Y-m-d H:i:s'),
                        //         ]);
                        // }
                        // else{
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
                                   'payment_terms_txt' => session('form_session_data.payment_terms_txt'),
                                   'delivery_time' => session('form_session_data.delivery_time'),
                                   'product_id' => $request->pid[$i],
                                   'qty' => $qty,
                                   'price' => $request->b_price[$i],
                                   'description' => $request->b_description[$i],
                                   'discount' => 0,
                                   'status' => 1,
                                   'sort_id' => 999,
                                   'created_by' => Auth::user()->id,
                               ]
                               );
                        //}

                    }
                }

            $bug = 0;
            } catch (\Throwable $e) {
                return $e;
                $bug = $e->errorInfo[1];
            }
            if($bug==0){
			    Toastr::success('Product Added Successfully', 'Success');
                return redirect('quote/chooseitems'); 
            }else {
                Toastr::error('Product Added Failed', 'Failed');
                return redirect()->back(); 
            }
    }
    
    public function deleteitems(Request $request)
    {
        $input = $request->all();
        
        try{        
            DB::table('sys_crm_quote_cart')->where('id', $request->id)->delete();
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

    public function generatequote(Request $request)
    {
        $cart=SysCrmQuoteCart::where(['cart_id' => session('logged_session_data.cart_id'),'user_id'=> Auth::user()->id])->get();
        $quote_id = SysCrmQuoteItems::where('deal_id',$cart[0]->deal_id)->max('quote_id');

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
                'payment_terms_txt' => $items->payment_terms_txt,
                'delivery_time' => $items->delivery_time,
                'product_id' => $items->product_id,
                'qty' => $items->qty,
                'price' => $items->price,
                'description' => $items->description,
                'discount' => $items->discount,
                'status' => $items->status,
                'sort_id' => $items->sort_id,
                'created_by' => Auth::user()->id,
                'quote_id' => $quote_id+1,
            ]);
            
            DB::table('sys_crm_deals')->where('id',$items->deal_id)
            ->update(['estimated_close_date' => date('Y-m-d', strtotime($items->delivery_date)),'quote_id' => $quote_id+1,'deal_discount' => 0]);
            SysHelper::deal_updated_at($request->deal_id);
        }
        
        DB::table('sys_crm_quote_cart')->where(['cart_id' => session('logged_session_data.cart_id'),'user_id'=> Auth::user()->id])->delete();
        
        return redirect('crm-deals/'.$items->deal_id.'/view');

    }
    
    public function quotedownload(Request $request, $id, $qid)
    {
        
        try {
        $quotation = SysCrmDeals::where('id',$id)->first();
        $quotationitems = SysCrmQuoteItems::where('deal_id',$id)->where('quote_id',$qid)->orderby('sort_id','ASC')->get();

       
        $currency=$quotationitems[0]->currency->code;
        $paymentterms=$quotationitems[0]->paymentterms->title;
        if(strtolower($quotationitems[0]->paymentterms->title)=="other"){
            $paymentterms=$quotationitems[0]->payment_terms_txt;
        }
        $deliverydate=$quotationitems[0]->delivery_date;
        $deliverytime=$quotationitems[0]->delivery_time;
        
        $pdfheader = $quotationitems[0]->company->pdf_header;
        $pdffooter = $quotationitems[0]->company->pdf_footer;
        $pdfwatermark = $quotationitems[0]->company->pdf_watermark;
        $pdffirstpage = $quotationitems[0]->company->pdf_first_page;
        
        /*if($quotationitems[0]->currency_id==1){ $net_vat=5; }
        else if($quotationitems[0]->currency_id==2){ $net_vat=5; }
        else if($quotationitems[0]->currency_id==3){ $net_vat=5; }
        else if($quotationitems[0]->currency_id==4){ $net_vat=15; }
        else if($quotationitems[0]->currency_id==5){ $net_vat=5; }
        else if($quotationitems[0]->currency_id==6){ $net_vat=18; }
        else if($quotationitems[0]->currency_id==7){ $net_vat=5; }
        else if($quotationitems[0]->currency_id==8){ $net_vat=5; }
        else if($quotationitems[0]->currency_id==9){ $net_vat=20; }*/
        $net_vat=$quotationitems[0]->vat;
        

        // $net_vat = $quotationitems[0]->company->net_vat;
        // if($net_vat==""){$net_vat=5;}

        $data = [
            'quotation'   => $quotation,
            'quotationitems'      => $quotationitems,
            'currency'      => $currency,
        ];
        //return $data;
        $wp = $request->with_partnumber;
        $wt = $request->without_total;
        $wv = $request->without_vat;
        
        if($request->without_vat==1){
            $net_vat=0;
        }

        //return view('backEnd.crm.QuotePDF',['quotation' => $quotation, 'quotationitems'=>$quotationitems, 'currency'=>$currency, 'paymentterms'=>$paymentterms, 'deliverydate'=>$deliverydate,'deliverytime'=>$deliverytime,'wp'=>$wp,'wt'=>$wt,'pdfheader'=>$pdfheader, 'pdffooter'=>$pdffooter, 'pdfwatermark'=>$pdfwatermark, 'pdffirstpage'=>$pdffirstpage, 'net_vat'=>$net_vat]);

        
        $pdf = PDF::loadView('backEnd.crm.QuotePDF2',['quotation' => $quotation, 'quotationitems'=>$quotationitems, 'currency'=>$currency, 'paymentterms'=>$paymentterms, 'deliverydate'=>$deliverydate,'deliverytime'=>$deliverytime,'wp'=>$wp,'wt'=>$wt, 'wv'=>$wv,'pdfheader'=>$pdfheader, 'pdffooter'=>$pdffooter, 'pdfwatermark'=>$pdfwatermark, 'pdffirstpage'=>$pdffirstpage, 'net_vat'=>$net_vat]);
        $pdf->setPaper('A4', 'portrait');
       
        $pageName = $quotationitems[0]->document_number." ".$quotation->customername->name.".pdf";
        
        return $pdf->download($pageName);

        //return view('admin.boq.form_pdf',['boq' => $boq, 'items'=>$items]);
            
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public function quotedownloadwp($id, $qid)
    {
        try {
        $quotation = SysCrmDeals::where('id',$id)->first();
        $quotationitems = SysCrmQuoteItems::where('deal_id',$id)->where('quote_id',$qid)->orderby('sort_id','ASC')->get();

        $currency=$quotationitems[0]->currency->code;
        $paymentterms=$quotationitems[0]->paymentterms->title;
        if(strtolower($quotationitems[0]->paymentterms->title)=="other"){
            $paymentterms=$quotationitems[0]->payment_terms_txt;
        }
        $deliverydate=$quotationitems[0]->delivery_date;
        $deliverytime=$quotationitems[0]->delivery_time;
        
        $pdfheader = $quotationitems[0]->company->pdf_header;
        $pdffooter = $quotationitems[0]->company->pdf_footer;
        $pdfwatermark = $quotationitems[0]->company->pdf_watermark;
        $pdffirstpage = $quotationitems[0]->company->pdf_first_page;

        if($quotationitems[0]->currency_id==1){ $net_vat=5; }
        else if($quotationitems[0]->currency_id==2){ $net_vat=5; }
        else if($quotationitems[0]->currency_id==3){ $net_vat=5; }
        else if($quotationitems[0]->currency_id==4){ $net_vat=15; }
        else if($quotationitems[0]->currency_id==5){ $net_vat=5; }
        else if($quotationitems[0]->currency_id==6){ $net_vat=18; }
        else if($quotationitems[0]->currency_id==7){ $net_vat=5; }
        else if($quotationitems[0]->currency_id==8){ $net_vat=5; }
        else if($quotationitems[0]->currency_id==9){ $net_vat=20; }
        else{ $net_vat=5; }

        // $net_vat = $quotationitems[0]->company->net_vat;
        // if($net_vat==""){$net_vat=5;}

        $data = [
            'quotation'   => $quotation,
            'quotationitems'      => $quotationitems,
            'currency'      => $currency,
        ];
        $wp = 1;
        $wt = 1;

        $pdf = PDF::loadView('backEnd.crm.QuotePDF',['quotation' => $quotation, 'quotationitems'=>$quotationitems, 'currency'=>$currency, 'paymentterms'=>$paymentterms, 'deliverydate'=>$deliverydate,'deliverytime'=>$deliverytime, 'wp'=>$wp, 'wt'=>$wt, 'pdfheader'=>$pdfheader, 'pdffooter'=>$pdffooter, 'pdfwatermark'=>$pdfwatermark, 'pdffirstpage'=>$pdffirstpage, 'net_vat'=>$net_vat]);
        $pdf->setPaper('A4', 'portrait');
        $pageName = "Quote-No-".$quotation->code.".pdf";
        return $pdf->download($pageName);
            
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public function quotedownloadev($id, $qid)
    {
        try {
        $quotation = SysCrmDeals::where('id',$id)->first();
        $quotationitems = SysCrmQuoteItems::where('deal_id',$id)->where('quote_id',$qid)->orderby('sort_id','ASC')->get();

        $currency=$quotationitems[0]->currency->code;
        $paymentterms=$quotationitems[0]->paymentterms->title;
        if(strtolower($quotationitems[0]->paymentterms->title)=="other"){
            $paymentterms=$quotationitems[0]->payment_terms_txt;
        }
        $deliverydate=$quotationitems[0]->delivery_date;
        $deliverytime=$quotationitems[0]->delivery_time;
        
        $pdfheader = $quotationitems[0]->company->pdf_header;
        $pdffooter = $quotationitems[0]->company->pdf_footer;
        $pdfwatermark = $quotationitems[0]->company->pdf_watermark;
        $pdffirstpage = $quotationitems[0]->company->pdf_first_page;
        
        if($quotationitems[0]->currency_id==1){ $net_vat=5; }
        else if($quotationitems[0]->currency_id==2){ $net_vat=5; }
        else if($quotationitems[0]->currency_id==3){ $net_vat=5; }
        else if($quotationitems[0]->currency_id==4){ $net_vat=15; }
        else if($quotationitems[0]->currency_id==5){ $net_vat=5; }
        else if($quotationitems[0]->currency_id==6){ $net_vat=18; }
        else if($quotationitems[0]->currency_id==7){ $net_vat=5; }
        else if($quotationitems[0]->currency_id==8){ $net_vat=5; }
        else if($quotationitems[0]->currency_id==9){ $net_vat=20; }
        else{ $net_vat=5; }

        //$net_vat = $quotationitems[0]->company->net_vat;
        //if($net_vat==""){$net_vat=5;}

        $data = [
            'quotation'   => $quotation,
            'quotationitems'      => $quotationitems,
            'currency'      => $currency,
        ];
        $wp = 1;
        $wt = 1;
        $net_vat = 0;
        
        $pdf = PDF::loadView('backEnd.crm.QuotePDF',['quotation' => $quotation, 'quotationitems'=>$quotationitems, 'currency'=>$currency, 'paymentterms'=>$paymentterms, 'deliverydate'=>$deliverydate,'deliverytime'=>$deliverytime, 'wp'=>$wp, 'wt'=>$wt, 'pdfheader'=>$pdfheader, 'pdffooter'=>$pdffooter, 'pdfwatermark'=>$pdfwatermark, 'pdffirstpage'=>$pdffirstpage, 'net_vat'=>$net_vat]);
        $pdf->setPaper('A4', 'portrait');
        $pageName = "Quote-No-".$quotation->code.".pdf";
        return $pdf->download($pageName);
            
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public function quoteedit(Request $request, $id, $qid)
    {
        try {
        $quotation = SysCrmDeals::where('id',$id)->first();
        $quotationitems = SysCrmQuoteItems::where('deal_id',$id)->where('quote_id',$qid)->orderby('sort_id','asc')->get();
        if(count($quotationitems)>1)
        {
            SysCrmQuoteItems::where('deal_id',$id)->where('quote_id',$qid)->where('status',0)->delete();
            $quotationitems = SysCrmQuoteItems::where('deal_id',$id)->where('quote_id',$qid)->orderby('sort_id','asc')->get();
        }

        if(count($quotationitems)>0){
            for($i = 0; $i < count($quotationitems); $i++)
            {
                DB::table('sys_crm_quote_items')->where('id',$quotationitems[$i]->id)
                         ->update([ 'sort_id' => $i+1,]);
            }
        }
        $paymenttermslist = SysPaymentTerms::get();

        $deal_id        =   $quotationitems[0]->deal_id;
        $company_id     =   $quotationitems[0]->company_id;
        $currency_id    =   $quotationitems[0]->currency_id;
        $customer_type  =   $quotationitems[0]->customer_type;
        $currency_code  =   $quotationitems[0]->currency->code;
        $payment_terms  =   $quotationitems[0]->payment_terms;
        $payment_terms_name  =   $quotationitems[0]->paymentterms->title;
        if(strtolower($quotationitems[0]->paymentterms->title)=="other"){
            $payment_terms_name=$quotationitems[0]->payment_terms_txt;
        }
        $payment_terms_txt  =   $quotationitems[0]->payment_terms_txt;
        $delivery_date  =   $quotationitems[0]->delivery_date;
        $delivery_time=$quotationitems[0]->delivery_time;
        
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
                    // ->orwhere('sm_items.description','like','%'.$request->part_number.'% ')->get();
                }
            } catch (\Throwable $th) {
                return $th;
            }
        }

        return view('backEnd.crm.QuoteEdit',compact('quotation','quotationitems','deal_id','company_id','currency_id','customer_type','currency_code','product','payment_terms','delivery_date','payment_terms_name','paymenttermslist','payment_terms_txt','delivery_time'));

        }    catch (\Throwable $th) {
            return redirect('crm-deals/'.$id.'/view');
        }
    }

    public function quoteaddnew(Request $request, $id, $qid)
    {
        try {
        $quotation = SysCrmDeals::where('id',$id)->first();
        $quotationitems = SysCrmQuoteItems::where('deal_id',$id)->where('quote_id',$qid)->orderby('sort_id','ASC')->get();

        $deal_id        =   $quotationitems[0]->deal_id;
        $company_id     =   $quotationitems[0]->company_id;
        $currency_id    =   $quotationitems[0]->currency_id;
        $customer_type  =   $quotationitems[0]->customer_type;
        $currency_code  =   $quotationitems[0]->currency->code;
        $payment_terms  =   $quotationitems[0]->payment_terms;
        $payment_terms_name  =   $quotationitems[0]->paymentterms->title;
        $quote_id  =   $quotationitems[0]->quote_id;
        if(strtolower($quotationitems[0]->paymentterms->title)=="other"){
            $payment_terms_name=$quotationitems[0]->payment_terms_txt;
        }
        $payment_terms_txt  =   $quotationitems[0]->payment_terms_txt;
        $delivery_date  =   $quotationitems[0]->delivery_date;
        $delivery_time=$quotationitems[0]->delivery_time;
        
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

        return view('backEnd.crm.QuoteEditNew',compact('quotation','quotationitems','deal_id','company_id','currency_id','customer_type','currency_code','product','payment_terms','delivery_date','payment_terms_name','payment_terms_txt','delivery_time','brands','itemCategories','SuCategories','quote_id'));

        } catch (\Throwable $th) {
            return redirect('crm-deals/'.$id.'/view');
        }
    }

    public function additemsedit(Request $request)
    {
        try {
            //$quote=SysCrmQuoteItems::select('id','qty')
            //->where(['deal_id' => $request->deal_id,'product_id'=> $request->id,'description' => $request->description])->first();

            // if(isset($quote)){
            //     DB::table('sys_crm_quote_items')->where('id',$quote->id)
            //         ->update([
            //             'qty' => $quote->qty + $request->qty,
            //             'price' => $request->price,
            //             'description' => $request->description,
            //             'updated_by' => Auth::user()->id,
            //             'updated_at' => date('Y-m-d H:i:s'),
            //         ]);
            // }
            // else{
            DB::table('sys_crm_quote_items')->insert(
                [
                    'user_id' => Auth::user()->id,
                    'deal_id' => $request->deal_id,
                    'company_id' => $request->company_id,
                    'currency_id' => $request->currency_id,
                    'customer_type' => $request->customer_type,
                    'payment_terms' => $request->payment_terms,
                    'delivery_date' => $request->delivery_date,
                    'payment_terms_txt' => $request->payment_terms_txt,
                    'delivery_time' => $request->delivery_time,
                    'product_id' => $request->id,
                    'qty' => $request->qty,
                    'price' => $request->price,
                    'description' => $request->description,
                    'discount' => 0,
                    'status' => 1,
                    'sort_id' => 999,
                    'created_by' => Auth::user()->id,
                    'quote_id' => $request->quote_id,
                ]
                );
                
                DB::table('sys_crm_deals')->where('id',$request->deal_id)
                ->update([ 'estimated_close_date' => date('Y-m-d', strtotime($request->delivery_date)),]);
                SysHelper::deal_updated_at($request->deal_id);

            //}
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
                    //$quote=SysCrmQuoteItems::select('id','qty')
                    //->where(['deal_id' => $request->b_deal_id,'product_id'=> $request->pid[$i],'description' => $request->b_description[$i]])->first();
                    // if(isset($quote)){
                    //     DB::table('sys_crm_quote_items')->where('id',$quote->id)
                    //         ->update([
                    //             'qty' => $quote->qty + $qty,
                    //             'price' => $request->b_price[$i],
                    //             'description' => $request->b_description[$i],
                    //             'updated_by' => Auth::user()->id,
                    //             'updated_at' => date('Y-m-d H:i:s'),
                    //         ]);
                    // }
                    // else{
                        DB::table('sys_crm_quote_items')->insert(
                            [
                                'user_id' => Auth::user()->id,
                                'deal_id' => $request->b_deal_id,
                                'company_id' => $request->b_company_id,
                                'currency_id' => $request->b_currency_id,
                                'customer_type' => $request->b_customer_type,
                                'payment_terms' => $request->b_payment_terms,
                                'delivery_date' => $request->b_delivery_date,
                                'payment_terms_txt' => $request->b_payment_terms_txt,
                                'delivery_time' => $request->b_delivery_time,
                                'product_id' => $request->pid[$i],
                                'qty' => $qty,
                                'price' => $request->b_price[$i],
                                'description' => $request->b_description[$i],
                                'discount' => 0,
                                'status' => 1,
                                'sort_id' => 999,
                                'created_by' => Auth::user()->id,
                                'quote_id' => $request->b_quote_id,
                            ]
                            );
                        //}

                        DB::table('sys_crm_deals')->where('id',$request->b_deal_id)
                        ->update([ 'estimated_close_date' => date('Y-m-d', strtotime($request->b_delivery_date)),]);
                        SysHelper::deal_updated_at($request->b_deal_id);
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
                    // $quote=SysCrmQuoteItems::select('id','qty')
                    // ->where(['deal_id' => $request->b_deal_id,'product_id'=> $request->pid[$i],'description' => $request->b_description[$i]])->first();
                    // if(isset($quote)){
                    //     DB::table('sys_crm_quote_items')->where('id',$quote->id)
                    //         ->update([
                    //             'qty' => $quote->qty + $qty,
                    //             'price' => $request->b_price[$i],
                    //             'description' => $request->b_description[$i],
                    //             'updated_by' => Auth::user()->id,
                    //             'updated_at' => date('Y-m-d H:i:s'),
                    //         ]);
                    // }
                    // else{
                        DB::table('sys_crm_quote_items')->insert(
                            [
                                'user_id' => Auth::user()->id,
                                'deal_id' => $request->b_deal_id,
                                'company_id' => $request->b_company_id,
                                'currency_id' => $request->b_currency_id,
                                'customer_type' => $request->b_customer_type,
                                'payment_terms' => $request->b_payment_terms,
                                'delivery_date' => $request->b_delivery_date,
                                'payment_terms_txt' => $request->b_payment_terms_txt,
                                'delivery_time' => $request->b_delivery_time,
                                'product_id' => $request->pid[$i],
                                'qty' => $qty,
                                'price' => $request->b_price[$i],
                                'description' => $request->b_description[$i],
                                'discount' => 0,
                                'status' => 1,
                                'sort_id' => 999,
                                'created_by' => Auth::user()->id,
                                'quote_id' => $request->b_quote_id,
                            ]
                            );
                            
                            DB::table('sys_crm_deals')->where('id',$request->b_deal_id)
                            ->update([ 'estimated_close_date' => date('Y-m-d', strtotime($request->b_delivery_date)),]);
                            SysHelper::deal_updated_at($request->b_deal_id);
                        //}
                }
            }
        $bug = 0;            
        } catch (\Throwable $e) {
            return $e;
            $bug = $e->errorInfo[1];
        }
        if($bug==0){
			Toastr::success('Product Added Successfully', 'Success');
            return redirect('crm-quote/'.$request->b_deal_id.'/edit/'.$request->b_quote_id); 
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
            SysHelper::deal_updated_at($request->deal_id);
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
            $chk = DB::table('sys_crm_quote_items')->where('quote_id', $request->quote_id)->where('deal_id', $request->deal_id)->count();
            if($chk==1) {
                DB::table('sys_crm_quote_items')->where('id', $request->id)->update(['status' => 0]);
            } else {
                DB::table('sys_crm_quote_items')->where('id', $request->id)->delete();
            }
            SysHelper::deal_updated_at($request->deal_id);
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
    public function itemsortup(Request $request)
    {        
        try{
            DB::table('sys_crm_quote_items')->where('deal_id',$request->deal_id)->where('sort_id',$request->sort_id-1)->update([ 'sort_id' => $request->sort_id,]);
            DB::table('sys_crm_quote_items')->where('id',$request->id)->update([ 'sort_id' => $request->sort_id-1,]);
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
    public function itemsortdown(Request $request)
    {        
        try{
            DB::table('sys_crm_quote_items')->where('deal_id',$request->deal_id)->where('sort_id',$request->sort_id+1)->update([ 'sort_id' => $request->sort_id,]);
            DB::table('sys_crm_quote_items')->where('id',$request->id)->update([ 'sort_id' => $request->sort_id+1,]);
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
            SysPriceBook::insert([
                'pid' => $items->id,
                'currency_id' => 6,
                'r_price' => '0.00',
                'e_price' => '0.00',
                'status' => 1,
                'created_by' => Auth::user()->id,
                'company_id' => session('logged_session_data.company_id')
            ]);
            SysPriceBook::insert([
                'pid' => $items->id,
                'currency_id' => 7,
                'r_price' => '0.00',
                'e_price' => '0.00',
                'status' => 1,
                'created_by' => Auth::user()->id,
                'company_id' => session('logged_session_data.company_id')
            ]);
            SysPriceBook::insert([
                'pid' => $items->id,
                'currency_id' => 8,
                'r_price' => '0.00',
                'e_price' => '0.00',
                'status' => 1,
                'created_by' => Auth::user()->id,
                'company_id' => session('logged_session_data.company_id')
            ]);
            SysPriceBook::insert([
                'pid' => $items->id,
                'currency_id' => 9,
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
            SysHelper::deal_updated_at($request->commentsid);
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
            DB::table('sys_crm_quote_items')->where('deal_id',$request->edit_deal_id)->where('quote_id',$request->edit_deal_quote_id)->update(
                [
                    'payment_terms' => $request->payment_terms,
                    'delivery_date' => date('Y-m-d', strtotime($request->delivery_date)),
                    'payment_terms_txt' => $request->payment_terms_txt,
                    'delivery_time' => $request->delivery_time,
                    'updated_by' => Auth::user()->id,
                ]
                );
                
            DB::table('sys_crm_deals')->where('id',$request->edit_deal_id)
            ->update([
                'quote_validity' => $request->quote_validity,
                'estimated_close_date' => date('Y-m-d', strtotime($request->delivery_date)),
            ]);
            SysHelper::deal_updated_at($request->edit_deal_id);
            Toastr::success('Updated successfully', 'Success');
            return redirect()->back();

        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
    public function updatetermsandcondition(Request $request)
    {
        try {
            DB::table('sys_crm_deals')->where('id',$request->edit_tc_deal_id)->where('quote_id',$request->edit_tc_deal_quote_id)->update(
                [
                    'terms_and_condition' => $request->terms_and_condition,
                    'updated_by' => Auth::user()->id,
                ]
                );
            
            SysHelper::deal_updated_at($request->edit_tc_deal_id);

            Toastr::success('Updated successfully', 'Success');
            return redirect()->back();

        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
    
    public function quotediscount(Request $request){
        try {
            DB::table('sys_crm_deals')->where('id',$request->dis_deal_id)->update([
                'deal_discount' => $request->deal_discount,
            ]);
            
            DB::table('sys_crm_quote_items')->where('deal_id',$request->dis_deal_id)->where('quote_id',$request->quote_dis_deal_id)->update([
                'quote_discount' => $request->deal_discount,
            ]);

            SysHelper::deal_updated_at($request->dis_deal_id);

            Toastr::success('Discount Added successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
    
    public function setprimary($id,$qid){
        try {
            
            $dis = SysCrmQuoteItems::select('quote_discount')->where('deal_id',$id)->where('quote_id',$qid)->first();

            DB::table('sys_crm_deals')->where('id',$id)->update(
            [
                'quote_id' => $qid,
                'deal_discount' => $dis->quote_discount,
            ]);

            SysHelper::deal_updated_at($id);


            Toastr::success('Quote No '.$qid.' successfully set as Primary Quotation', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
    public function createcopy($id,$qid){
        try {            
            $quote_id = SysCrmQuoteItems::where('deal_id',$id)->max('quote_id');
            $data = SysCrmQuoteItems::where('deal_id',$id)->where('quote_id',$qid)->get();
            $document_number = SysHelper::getNextDealQuoteDocNo();

            foreach ($data as $value) {
                DB::table('sys_crm_quote_items')->insert(
                    [
                        'user_id' => Auth::user()->id,
                        'deal_id' => $value->deal_id,
                        'company_id' => $value->company_id,
                        'currency_id' => $value->currency_id,
                        'customer_type' => $value->customer_type,
                        'payment_terms' => $value->payment_terms,
                        'delivery_date' => $value->delivery_date,
                        'payment_terms_txt' => $value->payment_terms_txt,
                        'delivery_time' => $value->delivery_time,
                        'product_id' => $value->product_id,
                        'qty' => $value->qty,
                        'price' => $value->price,
                        'description' => $value->description,
                        'discount' => $value->discount,
                        'status' => $value->status,
                        'sort_id' => $value->sort_id,
                        'created_by' => Auth::user()->id,
                        'quote_id' => $quote_id+1,
                        'document_number' => $document_number,
                    ]
                    );
                    
                    SysHelper::deal_updated_at($value->deal_id);
            }

            Toastr::success('Quote Copy Created Successfully', 'Success');
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


    public function get_deal_pdf_data($id)
    {
        try {
            $quotation = SysCrmDeals::where('id', $id)->first();
            $company = SysCompany::find($quotation->company_id);




            $qid = $quotation->quote_id;
            $quotationitems = SysCrmQuoteItems::where('deal_id', $id)->where('quote_id', $qid)->orderby('sort_id', 'ASC')->get();


            if (count($quotationitems) > 0) {
                $currency_modal = SysCurrency::find($quotationitems[0]->currency_id);
                $currency = optional($quotationitems[0]->currency)->code;
                $paymentterms = optional($quotationitems[0]->paymentterms)->title;
                if (strtolower(optional($quotationitems[0]->paymentterms)->title) == "other") {
                    $paymentterms = $quotationitems[0]->payment_terms_txt;
                }
                $deliverydate = $quotationitems[0]->delivery_date;
                $deliverytime = $quotationitems[0]->delivery_time;

                $pdfheader = optional($quotationitems[0]->company)->pdf_header;
                $pdffooter = optional($quotationitems[0]->company)->pdf_footer;
                $pdfwatermark = optional($quotationitems[0]->company)->pdf_watermark;
                $pdffirstpage = optional($quotationitems[0]->company)->pdf_first_page;
                /*if($quotationitems[0]->currency_id==1){ $net_vat=5; }
                else if($quotationitems[0]->currency_id==2){ $net_vat=5; }
                else if($quotationitems[0]->currency_id==3){ $net_vat=5; }
                else if($quotationitems[0]->currency_id==4){ $net_vat=15; }
                else if($quotationitems[0]->currency_id==5){ $net_vat=5; }
                else if($quotationitems[0]->currency_id==6){ $net_vat=18; }
                else if($quotationitems[0]->currency_id==7){ $net_vat=5; }
                else if($quotationitems[0]->currency_id==8){ $net_vat=5; }
                else if($quotationitems[0]->currency_id==9){ $net_vat=20; }*/
                $net_vat = $quotationitems[0]->vat;
            } else {
                $currency_modal = "";
                $currency = "";
                $paymentterms = "";
                $deliverydate = "";
                $deliverytime = "";
                $pdfheader = "syscom-pdf-header.jpg";
                $pdffooter = "syscom-pdf-footer.jpg";
                $pdfwatermark = "syscom-watermark-sm.png";
                $pdffirstpage = "syscom-pdf-first-page.jpg";
                $net_vat = 5;
            }


            $wp = 0;
            $wt = 0;
            $wv = 0;

            $enduser = SysCrmEndUser::where('deal_id', $id)->first();

            $support = SysCrmSupport::where('deal_id', $id)->get();
            $edit = SysCrmDeals::where('id', $id)->first();
            $collaboration = SysCrmDealsCollaboration::where('deal_id', $id)->get();
            $service = SysCrmService::where('deal_id', $id)->get();

            // $net_vat = $quotationitems[0]->company->net_vat;
            // if($net_vat==""){$net_vat=5;}

            //return $quotation; 


            $data = [
                'quotation' => $quotation,
                'quotationitems' => $quotationitems,
                'currency' => $currency,
                'currency_modal' => $currency_modal,
                'company' => $company,
                'paymentterms' => $paymentterms,
                'deliverydate' => $deliverydate,
                'deliverytime' => $deliverytime,
                'wp' => $wp,
                'wt' => $wt,
                'wv' => $wv,
                'pdfheader' => $pdfheader,
                'pdffooter' => $pdffooter,
                'pdfwatermark' => $pdfwatermark,
                'pdffirstpage' => $pdffirstpage,
                'net_vat' => $net_vat,
                'enduser' => $enduser,
                'support' => $support,
                'edit' => $edit,
                'service' => $service,
                'collaboration' => $collaboration,
            ];

            // return view('backEnd.crm.QuotePDFDeal', $data);
            
                $pdf = PDF::loadView('backEnd.crm.QuotePDFDeal', $data);
        $pdf->setPaper('A4', 'portrait');
       
        $pageName = $quotationitems[0]->document_number." ".$quotation->customername->name.".pdf";
        
        return $pdf->download($pageName);
        } catch (\Throwable $th) {
            dd($th);
            return [];
        }
    }
}

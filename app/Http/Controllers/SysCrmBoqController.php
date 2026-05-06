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
use App\SysCrmBoqCart;
use App\SysCrmBoqProductCart;
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
use Symfony\Component\Mime\Crypto\SMime;

class SysCrmBoqController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }

    public function create(Request $request){
        
        $brands = SysBrand::all();
        $itemCategories = SmItemCategory::all();
        $SuCategories = SmItemSubcategory::all();

        if($_POST){
            $form_data = [
                "company_id"    => $request->company_id,
                "cust_id"    => $request->cust_id,
                "currency_id"   => $request->currency_id,
                "customer_type" => $request->customer_type,                
                "deal_id"       => $request->deal_id,
                "payment_terms" => $request->payment_terms,
                "terms_and_condition" => $request->terms_and_condition,
                "quote_validity" => $request->quote_validity,
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
            return view('backEnd.crm.QuoteBOQNew',compact('brands','itemCategories','SuCategories'));
        }
        else{
            return view('backEnd.crm.QuoteBOQNew',compact('brands','itemCategories','SuCategories'));
        }
    }    

    public function chooseitems(Request $request)
    {
        if($_POST){
            try {

                $telephonetype = SysHelper::RemoveLastNullValue($request->telephonetype);
                $nolines = SysHelper::RemoveLastNullValue($request->nolines);

                SysHelper::AddLocationItems($request->nooflocation,session('form_session_data.customer_type'),$request->connectivity,session('form_session_data.company_id'));

                for($i=0; $i < count($telephonetype); $i++){
                    if($telephonetype[$i]=="Analog"){
                        if($nolines[$i] > 16 )
                        {
                            return redirect()->back()->with('error', 'Maximum Analog Line Number is 12.');
                        }
                        SysHelper::AddAnalogItems(session('form_session_data.customer_type'), $nolines[$i],session('form_session_data.company_id'));
                    }
                    if($telephonetype[$i]=="PRI"){
                        if($nolines[$i] > 40 )
                        {
                            return redirect()->back()->with('error', 'Maximum PRI Line Number is 40.');
                        }
                        SysHelper::AddPRIItems(session('form_session_data.customer_type'), $nolines[$i],session('form_session_data.company_id'));
                    }
                }

                $boq_cart=SysCrmBoqCart::select('id')->where(['cart_id' => session('logged_session_data.cart_id'),'user_id'=> Auth::user()->id])->first();

                if(isset($boq_cart)){
                    DB::table('boq_cart')->where('id',$boq_cart->id)
                        ->update([
                            'company_id' => session('form_session_data.company_id'),
                            'nooflocation' => $request->nooflocation,
                            'connectivity' => $request->connectivity,
                            'telephonetype' => rtrim(implode(",", $request->telephonetype),','),
                            'nolines' => rtrim(implode(",", $request->nolines),','),
                            'currency_id' => session('form_session_data.currency_id'),
                            'customer_type' => session('form_session_data.customer_type'),
                            'deal_id' => session('form_session_data.deal_id'),
                            'payment_terms' => session('form_session_data.payment_terms'),
                            'terms_and_condition'=> session('form_session_data.terms_and_condition'),
                            'quotevalidity' => session('form_session_data.quote_validity'),
                            'delivery_time' => session('form_session_data.delivery_time'),
                            'deliverydate' => session('form_session_data.delivery_date'),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'company' => session('logged_session_data.company_id'),
                        ]);
                }
                else{
                    DB::table('boq_cart')->insert([
                            'user_id' => Auth::user()->id,
                            'cart_id' => session('logged_session_data.cart_id'),
                            'company_id' => session('form_session_data.company_id'),
                            'nooflocation' => $request->nooflocation,
                            'connectivity' => $request->connectivity,
                            'telephonetype' => rtrim(implode(",", $request->telephonetype),','),
                            'nolines' => rtrim(implode(",", $request->nolines),','),
                            'currency_id' => session('form_session_data.currency_id'),
                            'customer_type' => session('form_session_data.customer_type'),
                            'deal_id' => session('form_session_data.deal_id'),
                            'payment_terms' => session('form_session_data.payment_terms'),
                            'terms_and_condition'=> session('form_session_data.terms_and_condition'),
                            'quotevalidity' => session('form_session_data.quote_validity'),
                            'delivery_time' => session('form_session_data.delivery_time'),
                            'deliverydate' => session('form_session_data.delivery_date'),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'company' => session('logged_session_data.company_id'),
                        ]);
                }

                return redirect()->route('boq.items');
            
            } catch (\Throwable $th) {
                return $th;
            }
        }

    }
    
    public function items()
    {

        try {
        $BCart = SysCrmBoqCart::select('id','company_id','nooflocation','connectivity','telephonetype','nolines','currency_id','customer_type','deal_id','company')
        ->where(['cart_id' => session('logged_session_data.cart_id'),'user_id'=> Auth::user()->id,'status'=> 1])->get();
       
        $PCart = SysCrmBoqProductCart::select('product_cart.id','product_cart.cart_id','product_cart.qty','product_cart.price','sm_items.part_number','sm_items.description')
            ->join('sm_items','sm_items.id','product_cart.product_id')
            ->where(['product_cart.cart_id' => session('logged_session_data.cart_id'),'product_cart.user_id'=> Auth::user()->id,'product_cart.status'=> 1])->get();

        SysHelper::PhoneLicense($BCart[0]->customer_type,$BCart[0]->company_id);
        
        $currency=SysHelper::get_currency(session('form_session_data.currency_id'));

        if(session('form_session_data.customer_type')==1){

            $BasicPhones = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.r_price as price')
                ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->wherein('part_number',SysHelper::GetBasicPhones())->get();

            $ReceptionModule = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.r_price as price')
                ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->where('part_number','700514337')->get();

            $ConfrencePhone = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.r_price as price')
                ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->wherein('part_number',SysHelper::GetConfrencePhone())->get();
                
            $ManagerLevelPhones = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.r_price as price')
            ->join('sys_price_book','sys_price_book.pid','sm_items.id')
            ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->wherein('part_number',SysHelper::GetManagerLevelPhones())->get();
            
            $CRMorERP = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.r_price as price')
            ->join('sys_price_book','sys_price_book.pid','sm_items.id')
            ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->where('part_number','382689')->get();

            $Softphone = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.r_price as price')
            ->join('sys_price_book','sys_price_book.pid','sm_items.id')
            ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->wherein('part_number',['396447','396316'])->get();

            $CallRecording = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.r_price as price')
            ->join('sys_price_book','sys_price_book.pid','sm_items.id')
            ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->wherein('part_number',['393296','396447'])->get();

            $CallBilling = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.r_price as price')
            ->join('sys_price_book','sys_price_book.pid','sm_items.id')
            ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->wherein('part_number',['399530'])->get();

            $WelcomeMessage = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.r_price as price')
            ->join('sys_price_book','sys_price_book.pid','sm_items.id')
            ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->wherein('part_number',['396447'])->get();

            $PartyPhone = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.r_price as price')
            ->join('sys_price_book','sys_price_book.pid','sm_items.id')
            ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->wherein('part_number',['383072'])->get();

        }
        else{
            $BasicPhones = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.e_price as price')
                ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->wherein('part_number',SysHelper::GetBasicPhones())->get();
                
            $ReceptionModule = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.e_price as price')
            ->join('sys_price_book','sys_price_book.pid','sm_items.id')
            ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->where('part_number','700514337')->get();
            
            $ConfrencePhone = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.e_price as price')
                ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->wherein('part_number',SysHelper::GetConfrencePhone())->get();
                
            $ManagerLevelPhones = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.e_price as price')
            ->join('sys_price_book','sys_price_book.pid','sm_items.id')
            ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->wherein('part_number',SysHelper::GetManagerLevelPhones())->get();
            
            $CRMorERP = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.e_price as price')
            ->join('sys_price_book','sys_price_book.pid','sm_items.id')
            ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->where('part_number','382689')->get();

            $Softphone = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.e_price as price')
            ->join('sys_price_book','sys_price_book.pid','sm_items.id')
            ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->wherein('part_number',['396447','396316'])->get();

            $CallRecording = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.e_price as price')
            ->join('sys_price_book','sys_price_book.pid','sm_items.id')
            ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->wherein('part_number',['393296','396447'])->get();

            $CallBilling = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.e_price as price')
            ->join('sys_price_book','sys_price_book.pid','sm_items.id')
            ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->wherein('part_number',['399530'])->get();

            $WelcomeMessage = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.e_price as price')
            ->join('sys_price_book','sys_price_book.pid','sm_items.id')
            ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->wherein('part_number',['396447'])->get();

            $PartyPhone = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.e_price as price')
            ->join('sys_price_book','sys_price_book.pid','sm_items.id')
            ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->wherein('part_number',['383072'])->get();

        }

        } catch (\Throwable $th) {
            return $th;
        }

        return view('backEnd.crm.QuoteBOQNewItems',['BasicPhones' => $BasicPhones, 'ReceptionModule' => $ReceptionModule,'ConfrencePhone' => $ConfrencePhone, 'ManagerLevelPhones' => $ManagerLevelPhones, 'CRMorERP'=> $CRMorERP, 'Softphone' => $Softphone, 'CallRecording' => $CallRecording, 'CallBilling' => $CallBilling,'WelcomeMessage' => $WelcomeMessage, 'PartyPhone'=>$PartyPhone, 'BCart'=> $BCart, 'Cart' => $PCart, 'currency' => $currency]);
    }
    
    
    public function addtocart(Request $request)
    {
        $input = $request->all();
        
        try{
        $cart=SysCrmBoqProductCart::select('id','qty')
            ->where(['cart_id' => session('logged_session_data.cart_id'),'user_id'=> Auth::user()->id,'product_id'=> $request->id])
            ->first();

        if(isset($cart)){
            DB::table('product_cart')->where('id',$cart->id)
                ->update([
                    'qty' => $cart->qty + $request->qty,
                    'price' => $request->price,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }
        else{
            DB::table('product_cart')->insert(
                [
                    'user_id' => Auth::user()->id,
                    'cart_id' => session('logged_session_data.cart_id'),
                    'product_id' => $request->id,
                    'qty' => $request->qty,
                    'price' => $request->price,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }
            $bug = 0;
            SysHelper::PhoneLicense($request->customer_type,$request->company);
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
    
    public function addtocartgroup(Request $request)
    {

        if($request->id =='b1'){ $pro=['700513907','700512398','700512402'];}
        if($request->id =='b2'){ $pro=['700513907','700515454'];}
        if($request->id =='b3'){ $pro=['700513905','700512398'];}
        if($request->id =='b4'){ $pro=['700513905','700515454'];}

        $input = $request->all();
        
        if($request->customer_type==1){
            $pro = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.e_price as price')
            ->join('sys_price_book','sys_price_book.pid','sm_items.id')
            ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->wherein('part_number',$pro)->get();
        }else{
            $pro = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.e_price as price')
            ->join('sys_price_book','sys_price_book.pid','sm_items.id')
            ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->wherein('part_number',$pro)->get();
        }

            foreach ($pro as $p) {
                $cart=SysCrmBoqProductCart::select('id','qty')
                    ->where(['cart_id' => session('logged_session_data.cart_id'),'user_id'=> Auth::user()->id,'product_id'=> $p->id])
                    ->first();

                if(isset($cart)){
                    DB::table('product_cart')->where('id',$cart->id)
                        ->update([
                            'qty' => $cart->qty + $request->qty,
                            'price' => $p->price,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }
                else{
                    DB::table('product_cart')->insert(
                        [
                            'user_id' => Auth::user()->id,
                            'cart_id' => session('logged_session_data.cart_id'),
                            'product_id' => $p->id,
                            'qty' => $request->qty,
                            'price' => $p->price,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }

            }

        try{        
            $bug = 0;
            SysHelper::PhoneLicense($request->customer_type,$request->company);
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

    public function deltocart(Request $request)
    {
        $input = $request->all();
        
        try{
            DB::table('product_cart')->where('id', $request->id)->delete();
            $bug = 0;            
            SysHelper::PhoneLicense($request->customer_type,$request->company);
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
        $cart=SysCrmBoqProductCart::where(['cart_id' => session('logged_session_data.cart_id'),'user_id'=> Auth::user()->id])->get();
        //return $cart;
        $boq=SysCrmBoqCart::where(['cart_id' => session('logged_session_data.cart_id'),'user_id'=> Auth::user()->id])->first();
        //return $boq;
        $quote_id = SysCrmQuoteItems::where('deal_id',$boq->deal_id)->max('quote_id');

        foreach($cart as $items)
        {
            $pro = SmItem::select('description')->where('id',$items->product_id)->first();
            DB::table('sys_crm_quote_items')->insert([
                'user_id' => $items->user_id,
                'deal_id' => $boq->deal_id,
                'company_id' => $boq->company_id,
                'currency_id' => $boq->currency_id,
                'customer_type' => $boq->customer_type,
                'payment_terms' => $boq->payment_terms,
                'delivery_date' => $boq->deliverydate,
                'delivery_time' => $boq->delivery_time,
                'product_id' => $items->product_id,
                'qty' => $items->qty,
                'price' => $items->price,
                'description' => $pro->description,
                'discount' => $items->discount,
                'status' => $items->status,
                'sort_id' => 0,
                'created_by' => Auth::user()->id,
                'quote_id' => $quote_id+1,
                'nooflocation' => $boq->nooflocation,
                'connectivity' => $boq->connectivity,
                'telephonetype' => $boq->telephonetype,
                'nolines' => $boq->nolines,
            ]);
            
            DB::table('sys_crm_deals')->where('id',$boq->deal_id)
            ->update(['estimated_close_date' => date('Y-m-d', strtotime($items->deliverydate)),'quote_id' => $quote_id+1,'deal_discount' => 0]);
        }
        
        DB::table('boq_cart')->where(['cart_id' => session('logged_session_data.cart_id'),'user_id'=> Auth::user()->id])->delete();
        DB::table('product_cart')->where(['cart_id' => session('logged_session_data.cart_id'),'user_id'=> Auth::user()->id])->delete();
        
        return redirect('crm-deals/'.$boq->deal_id.'/view');
    }
    
}
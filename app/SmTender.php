<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;

class SmTender extends Model
{
    public function customer(){
    	return $this->belongsTo('App\SmStaff', 'customer_id', 'id');
    }

    public function vendor(){
    	return $this->belongsTo('App\SmSupplier', 'vendor_id', 'id');
    }

    public function createdBy(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }

    public function tenderProducts()
    {
        return $this->hasMany('App\SmTenderProduct', 'tender_id', 'id');
    }


    public static function productDetail($product_id, $tender_id)
    { 

        $r = DB::table('sm_item_receives')->select('sm_item_receives.*','sm_tender_products.product_model as productModel')
                        ->join('sm_tender_products', 'sm_tender_products.product_id', '=','sm_item_receives.product_id')
                        ->where([['sm_tender_products.tender_id',$tender_id],['sm_tender_products.product_id',$product_id]])
                        ->first();
             
        $result=  SmItemReceive::where('product_id', $product_id)->orderBy('id', 'desc')->first();
        
        return $r;

    }
}

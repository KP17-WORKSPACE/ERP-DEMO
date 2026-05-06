<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class SmQuotation extends Model
{
    public function customer()
    {
        return $this->belongsTo('App\SmStaff', 'customer_id', 'id');
    }

    public function vendor()
    {
        return $this->belongsTo('App\SmSupplier', 'vendor_id', 'id');
    }

    public function quotationProducts()
    {
        return $this->hasMany('App\SmQuotationProducts', 'quotation_id', 'id');
    }


    public static function productDetail($product_id, $quotation_id)
    {

        $r = DB::table('sm_item_receives')
            ->select('sm_item_receives.*', 'sm_quotation_products.product_model as productModel', 'sm_quotation_products.*')
            ->join('sm_quotation_products', 'sm_quotation_products.product_id', '=', 'sm_item_receives.product_id')
            ->where([['sm_quotation_products.quotation_id', $quotation_id], ['sm_quotation_products.product_id', $product_id]])
            ->first();

        $result =  SmItemReceive::where('product_id', $product_id)->orderBy('id', 'desc')->first();

        return $r;
    }

    public static function productQuantity($product_id)
    {

        $total_quantity =  SmItemReceive::where('product_id', $product_id)->sum('total_quantity');


        return $total_quantity;
    }

    public static function bid_amount($total, $discount_amount, $type){

    	if($type != ""){
    		if($type == "P"){

    			$percentage = $total / 100 * $discount_amount;
                $total = $total - $percentage;

    		}elseif($type == "A"){

    			$total = $total - $discount_amount;
    		}
    	}

    	return $total;
    }

}

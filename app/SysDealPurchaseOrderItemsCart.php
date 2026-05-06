<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysDealPurchaseOrderItemsCart extends Model
{
    protected $table = 'sys_deal_purchase_order_items_cart';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','cart_id','po_id','quote_item_id','part_number','part_number_txt','description','tax','qty','unitprice','value','discount','fright','customcharges','taxableamount','vatamount','status','refid','deal_id','deal_qty','po_qty'
    ];    
}
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPurchaseOrderItemsCart extends Model
{
    protected $table = 'sys_purchase_order_items_cart';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','cart_id','po_id','part_number','description','tax','qty','unitprice','value','discount','fright','customcharges','taxableamount','vatamount','status','refid','sort_id'
    ];
    
    public function productname(){
        return $this->belongsTo('App\SmItem', 'part_number', 'id');
    }
}
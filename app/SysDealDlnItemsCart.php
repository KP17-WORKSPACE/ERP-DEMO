<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysDealDlnItemsCart extends Model
{
    protected $table = 'sys_deal_dln_items_cart';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','cart_id','dln_id','part_number','part_number_txt','description','tax','qty','unitprice','value','discount','fright','customcharges','taxableamount','vatamount','status','refid','deal_id','deal_qty','dln_qty'
    ];    
}
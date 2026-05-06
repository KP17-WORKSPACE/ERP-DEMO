<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysGRNItemsCart extends Model
{
    protected $table = 'sys_grn_items_cart';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','cart_id','grn_id','part_number','tax','qty','unitprice','value','discount','customcharges','taxableamount','vatamount','status'
    ];    
}
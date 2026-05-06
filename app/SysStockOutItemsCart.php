<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysStockOutItemsCart extends Model
{
    protected $table = 'sys_stock_out_items_cart';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','cart_id','part_number','part_number_txt','description','qty','unitprice','value','status','created_by','updated_by','created_at','updated_at','refid','serialno','narration'
    ];    
}
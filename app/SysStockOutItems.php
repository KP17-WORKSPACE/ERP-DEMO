<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysStockOutItems extends Model
{
    protected $table = 'sys_stock_out_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','stock_out_id','part_number','part_number_txt','description','qty','unitprice','value','status','created_by','updated_by','created_at','updated_at','refid','serialno','narration'
    ];    
}
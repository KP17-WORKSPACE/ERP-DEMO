<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysSalesInvoiceItemsCart extends Model
{
    protected $table = 'sys_sales_invoice_items_cart';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','cart_id','si_id','part_number','tax','qty','unitprice','value','discount','taxableamount','vatamount','status','refid','serialno','sort_id','cost'
    ];    
}
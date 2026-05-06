<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysItemStockImport extends Model
{
    protected $table = 'sys_item_stock_import';
    protected $primaryKey = 'id';

    protected $fillable = [
        'partno', 'description','slno','qty_in','price_in','remarks','status','created_by','created_at','company_id','currency_id','import_date'
    ];

}
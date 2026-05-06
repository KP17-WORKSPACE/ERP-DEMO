<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysStockOut extends Model
{
    protected $table = 'sys_stock_out';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'date','doc_number','remarks','currancy','status','created_by','updated_by','created_at','updated_at','company_id','customer_id','supplier_id',
    ];
    
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
      public function currency_name(){
		  return $this->belongsTo('App\SysCurrencySettings', 'currancy', 'id');
    }
}
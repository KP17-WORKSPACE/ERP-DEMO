<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysSalesReturnListCart extends Model
{
    protected $table = 'sys_sales_return_list_cart';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','cart_id','sr_id','part_number','tax','qty','unitprice','value','discount','customcharges','taxableamount','vatamount','status','delivery_status','sort_id'
    ];

    public function accountid(){
	    return $this->belongsTo('App\SysChartofAccounts', 'account_id', 'id');
    }
    public function product(){
        return $this->belongsTo('App\SmItem', 'part_number', 'id');
    }
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }
    
}

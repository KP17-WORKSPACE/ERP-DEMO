<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPurchaseReturnListCart extends Model
{
    protected $table = 'sys_purchase_return_list_cart';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','cart_id','pr_id','pi_id_ref','part_number','qty','unitprice','value','discount','taxableamount','vat','vatamount','remarks','serialno', 'status','created_by','updated_by','created_at','updated_at','sort_id'
    ];

    public function partnumber(){
	    return $this->belongsTo('App\SmItem', 'part_number', 'id');
    }
    public function accountid(){
	    return $this->belongsTo('App\SysChartofAccounts', 'account_id', 'id');
    }
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }
    
}

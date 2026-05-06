<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPurchaseReturnList extends Model
{
    protected $table = 'sys_purchase_return_list';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'pr_id','pi_id_ref','partno','qty','unitprice','value','discount','taxableamount','vat','vatamount','remarks','serialno', 'status','created_by','updated_by','created_at','updated_at','sort_id','description'
    ];

    public function partnumber(){
	    return $this->belongsTo('App\SmItem', 'partno', 'id');
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

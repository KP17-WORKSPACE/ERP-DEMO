<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmQuoteCharges extends Model
{
    protected $table = 'sys_crm_quote_charges';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'deal_id','quote_id','selling_exp_account','credit_account','amount','remarks','status','created_by','updated_by','created_at','updated_at'
    ];
    
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function sellingexpaccount(){
        return $this->belongsTo('App\SysChartofAccounts', 'selling_exp_account', 'id');
    }
    public function creditaccount(){
        return $this->belongsTo('App\SysChartofAccounts', 'credit_account', 'id');
    }
}
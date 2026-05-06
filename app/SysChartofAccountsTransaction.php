<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysChartofAccountsTransaction extends Model
{
    protected $table = 'sys_chartofaccounts_transaction';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','account_id','transaction_id','transaction_no','transaction_date','transaction_type','debit_amount','credit_amount','remarks','created_by','created_at','updated_by','updated_at','status','plan','company_id','transaction_ref','entry_no','is_main_account'
    ];

    public function accounts(){
	    return $this->belongsTo('App\SysChartofAccounts', 'account_id', 'id');
    }
    public function accounts2(){
	    return $this->belongsTo('App\SysCustSuppl', 'account_id', 'id');
    }
}

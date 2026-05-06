<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysChartofaccountsOpeningBalanceInvoice extends Model
{
    protected $table = 'sys_chartofaccounts_opening_balance_invoice';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','account_id','account_code','account_name','invoice_date','invoice_no','debit_amount','credit_amount','company_id','status','created_by','created_at','updated_by','updated_at'
    ];

    public function accountid(){
	    return $this->belongsTo('App\SysChartofAccounts', 'account_id', 'id');
	}
    public function accountcode(){
	    return $this->belongsTo('App\SysChartofAccounts', 'account_code', 'id');
	}
}

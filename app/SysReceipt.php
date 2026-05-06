<?php

namespace App;

use App\SysChartofAccountsTransaction;
use Illuminate\Database\Eloquent\Model;

class SysReceipt extends Model
{
    protected $table = 'sys_receipt';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'doc_number','doc_date','mode','receipt_mode','receipt_through','cheque_date','cheque_number','cheque_bank_name','receipt_date','currency','narration','status','created_by','updated_by','created_at','updated_at','company_id','deal_id','edit_note','pdc_removed_os'
    ];

    public function account(){
        return $this->belongsTo('App\SysChartofAccounts', 'receipt_mode', 'id');
    }
    
    public function deal_code(){
        return $this->belongsTo('App\SysCrmDeals', 'deal_id', 'id');
    }
    
    public function receiptmode(){
	     return $this->belongsTo('App\SysReceiptMode', 'receipt_mode', 'id');
    }
    public function receiptmodeacc(){
	     return $this->belongsTo('App\SysChartofAccounts', 'receipt_mode', 'id');
    }

    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }

    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }
    public function currency_name(){
		  return $this->belongsTo('App\SysCurrencySettings', 'currency', 'id');
    }
    public function attachments()
    {
        return $this->hasMany('App\SysReceiptAttachment', 'sys_receipt_id', 'id');
    }

    public function getFirstAccountNameAttribute()
    {
        $transaction = SysChartofAccountsTransaction::where('transaction_id', $this->id)
            ->whereIn('transaction_type', ['cashreceipt', 'bankreceipt'])
            ->where('is_main_account', 0)
            ->with('accounts')
            ->first();

        if(SysHelper::getCompanyCodeSettings()['is_account_code']){
            return $transaction && $transaction->accounts ?  $transaction->accounts->account_name.' ('.$transaction->accounts->account_code.')' : '';
        }

        return $transaction && $transaction->accounts ? $transaction->accounts->account_name : '';
    }
}
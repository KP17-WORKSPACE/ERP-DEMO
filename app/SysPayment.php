<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPayment extends Model
{
    protected $table = 'sys_payment';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'doc_number','doc_date','mode','payment_mode','payment_through','cheque_date','cheque_number','cheque_bank_name','payment_date','currency','narration','status','created_by','updated_by','created_at','updated_at','company_id','deal_id','pdc_removed_os','cheque_id','edit_note','no_days','chequebook_id','cheque_status'
    ];

    public function account(){
        return $this->belongsTo('App\SysChartofAccounts', 'payment_mode', 'id');
    }
    
    public function deal_code(){
        return $this->belongsTo('App\SysCrmDeals', 'deal_id', 'id');
    }

    public function receiptmode(){
	     return $this->belongsTo('App\SysReceiptMode', 'payment_mode', 'id');
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

    public function chequebook(){
        return $this->belongsTo('App\Chequebook', 'chequebook_id', 'id');
    }

    public function attachments(){
        return $this->hasMany('App\SysPaymentAttachment', 'sys_payment_id', 'id');
    }
}
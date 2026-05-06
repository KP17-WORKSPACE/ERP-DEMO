<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysBankPayment extends Model
{
    protected $table = 'sys_bankpayment';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'doc_number','doc_date','payment_mode','cheque_date','cheque_number','cheque_bank_name','currency','narration','status','created_by','updated_by','created_at','updated_at','company_id'
    ];
    public function account(){
        return $this->belongsTo('App\SysChartofAccounts', 'payment_mode', 'id');
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
    
}

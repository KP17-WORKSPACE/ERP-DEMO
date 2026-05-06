<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPaymentCheque extends Model
{
    protected $table = 'sys_payment_cheque';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'doc_number','doc_date','bank_name','cheque_number','cheque_date','supplier_name','other_supplier_name','amount','amount_words','deal_id','attachment','status','created_by','updated_by','created_at','updated_at','company_id','reference','cheque_id','sys_payment_id'
    ];

    public function bankname(){
        return $this->belongsTo('App\SysChartofAccounts', 'bank_name', 'id');
    }
    public function suppliername(){
        return $this->belongsTo('App\SysChartofAccounts', 'supplier_name', 'id');
    }

    public function deal_code(){
        return $this->belongsTo('App\SysCrmDeals', 'deal_id', 'id');
    }

    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }

    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }

    public function cheque(){
        return $this->belongsTo('App\Chequebook', 'cheque_id', 'id');
    }

    public function payment(){
        return $this->belongsTo('App\SysPayment', 'sys_payment_id', 'id');
    }
}
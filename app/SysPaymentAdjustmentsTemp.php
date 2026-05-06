<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysPaymentAdjustmentsTemp extends Model
{
    protected $table = 'sys_payment_adjustments_temp';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','transaction_type','bi_cheque_amount','bi_amount_adjusted','bi_balance_to_adjust','bi_currency','bi_doc_number','bi_contains','bi_doc_no','bi_doc_date','bi_lpo_no','bi_bill_number','bi_total','bi_paid','bi_balance','bi_amount','bi_narration','status','created_by','updated_by','created_at','updated_at','process_id','process_status','company_id','account_id','bi_extra_amount','deal_id','deal_code'
    ];

    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }
    
}

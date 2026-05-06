<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmDealTrackApprovalReceivables extends Model
{
    protected $table = 'sys_crm_deal_track_approval_receivables';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','deal_track_id','deal_id','payment_collection','payment_status','remarks','status','created_by','created_at','updated_by','updated_at','paymenttype','amount','balance_amount','thousand','fivehundred','hundred','fifty','twenty','ten','five','one','fiftyp','twentyfivep','cash_date','cheque_no','cheque_date','cheque_copy','bank_name','deposit_date','open_credit_date','credit_card_type','payment_date','credit_card_deposit_date','banktt_copy','banktt_date','reminder_date','credit_note','amount2','amount3','cash_date2','cash_date3','cheque_no2','cheque_no3','cheque_date2','cheque_date3','banktt_date2','banktt_date3','deposit_date2','created_date','doc_number','receipt_mode','receipt_date','invoice_no','receipt_through'
    ];
        
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }
    public function receiptmode(){
        return $this->belongsTo('App\SysChartofAccounts', 'receipt_mode', 'id');
    }

}
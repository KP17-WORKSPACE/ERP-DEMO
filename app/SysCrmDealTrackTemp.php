<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmDealTrackTemp extends Model
{
    protected $table = 'sys_crm_deal_track_temp';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'deal_id','delivery_date','payment_terms','payment_mode','payment_mode_sec','lpo','purchease_quote','cheque_copy','purchease_required','remarks','special_instruction','status','created_by','updated_by','created_at','updated_at','company_id','accounts','sales','purchease','invoice','delivery','tech','partial_delivery','technical','technical_detail','reference_no','reference_date','purchease_approval','invoice_approval','delivery_approval','receivables_approval','start_date','end_date','created_date','invoicing'
    ];
    
    public function deal_code(){
        return $this->belongsTo('App\SysCrmDeals', 'deal_id', 'id');
    }

    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function paymentterms(){
        return $this->belongsTo('App\SysPaymentTerms', 'payment_terms', 'id');
    }
    public function dealid(){
        return $this->belongsTo('App\SysCrmDeals', 'deal_id', 'id');
    }
    public function ownername(){
        return $this->belongsTo('App\SmStaff', 'owner', 'user_id');
    }
    public function customername(){
        return $this->belongsTo('App\SysCustSuppl', 'cust_id', 'id');
    }
    public function account(){
        return $this->belongsTo('App\SysCrmDealTrackApprovalAccounts', 'deal_id', 'deal_id');
    }
    public function sales(){
        return $this->belongsTo('App\SysCrmDealTrackApprovalSales', 'deal_id', 'deal_id');
    }
    public function purchease(){
        return $this->belongsTo('App\SysCrmDealTrackApprovalPurchease', 'deal_id', 'deal_id');
    }
    public function invoice(){
        return $this->belongsTo('App\SysCrmDealTrackApprovalInvoice', 'deal_id', 'deal_id');
    }
    public function delivery(){
        return $this->belongsTo('App\SysCrmDealTrackApprovalDelivery', 'deal_id', 'deal_id');
    }
}
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmDealTrackApprovalInvoice extends Model
{
    protected $table = 'sys_crm_deal_track_approval_invoice';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'deal_track_id','deal_id','delivery_advice','validation','hold','print','remarks','status','created_by','updated_by','created_at','updated_at','invoice_no','partial_invoice','partial_invoice_amount','created_date'
    ];
        
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }
}
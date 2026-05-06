<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmDealTrackApprovalDelivery extends Model
{
    protected $table = 'sys_crm_deal_track_approval_delivery';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'deal_track_id','deal_id','do_status','do_no','print_invoice_no','cheque_collection','cheque_collection_file','delivery_status','deliver_by','driver','remarks','status','created_by','updated_by','created_at','updated_at','cash_collected','contact_no','id_no','attach_file','awb_no','created_date'
    ];
        
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }
}
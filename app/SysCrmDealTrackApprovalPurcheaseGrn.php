<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmDealTrackApprovalPurcheaseGrn extends Model
{
    protected $table = 'sys_crm_deal_track_approval_purchease_grn';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'deal_id','deal_track_id','grn_no','remarks','status','created_by','created_at','updated_by','updated_at'
    ];
        
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }
}
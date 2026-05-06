<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmDealTrackApprovalTechnical extends Model
{
    protected $table = 'sys_crm_deal_track_approval_technical';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'deal_track_id','deal_id','technical_approve','remarks','status','created_by','updated_by','created_at','updated_at'
    ];
        
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }
}
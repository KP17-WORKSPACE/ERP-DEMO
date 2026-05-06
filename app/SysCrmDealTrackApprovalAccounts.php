<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmDealTrackApprovalAccounts extends Model
{
    protected $table = 'sys_crm_deal_track_approval_accounts';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'deal_track_id','deal_id','customer_status','credit_limit','payment_terms','pending_payment','other','remarks','status','created_by','updated_by','created_at','updated_at', 'created_date'
    ];
        
    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');
    }
    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'user_id');
    }

    public function dealTrack()
    {
        return $this->belongsTo(SysCrmDealTrack::class, 'deal_track_id');
    }
}
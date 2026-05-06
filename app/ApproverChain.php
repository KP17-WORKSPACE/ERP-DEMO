<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApproverChain extends Model
{
    //

  protected $fillable = [
  'leave_request_id',
  'staff_id',
  'reporting_manager_id',
  'hr_id',
  'accounts_id',
];


 public function reportingManager()
    {
        return $this->belongsTo(\App\SmStaff::class, 'reporting_manager_id');
    }

    public function hr()
    {
        return $this->belongsTo(\App\SmStaff::class, 'hr_id');
    }

    public function accounts()
    {
        return $this->belongsTo(\App\SmStaff::class, 'accounts_id');
    }

     public function steps()
    {
        return $this->hasMany(HrmsApproverChainStep::class, 'approver_chain_id');
    }

    public function leaveRequest()
    {
        return $this->belongsTo(SmLeaveRequest::class, 'leave_request_id');
    }

    public function leave()
    {
    return $this->belongsTo(\App\SmLeaveRequest::class, 'leave_request_id');
    }



}

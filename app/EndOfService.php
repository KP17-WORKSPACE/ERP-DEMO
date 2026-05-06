<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EndOfService extends Model
{
    protected $table = 'sm_end_of_service';
    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(SmStaff::class, 'employee_id');
    }

    public function notice()
    {
        return $this->hasOne(EndOfServiceNotice::class, 'end_of_service_id');
    }

    public function handover()
    {
        return $this->hasOne(EndOfServiceHandover::class, 'end_of_service_id');
    }

    public function assetClearance()
    {
        return $this->hasOne(EndOfServiceAssetClearance::class, 'end_of_service_id');
    }

    public function finance()
    {
        return $this->hasOne(EndOfServiceFinance::class, 'end_of_service_id');
    }

    public function finalSettlement()
    {
        return $this->hasOne(EndOfServiceFinalSettlement::class, 'end_of_service_id');
    }

    public function exitInterview()
    {
        return $this->hasOne(EndOfServiceExitInterview::class, 'end_of_service_id');
    }

    public function approvals()
    {
        return $this->hasMany(EndOfServiceApproval::class, 'end_of_service_id');
    }

    public function documents()
    {
        return $this->hasMany(EndOfServiceDocument::class, 'end_of_service_id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HrmsApproverChain extends Model
{
    //

    protected $table = 'hrms_approver_chains';

    protected $fillable = [
        'leave_request_id',
        'staff_id',
        'overall_status',
    ];

    public function steps()
    {
        return $this->hasMany(HrmsApproverChainStep::class, 'approver_chain_id')
                    ->orderBy('step_no');
    }

    public function currentStep()
    {
        return $this->steps()->where('status', 'P')->orderBy('step_no')->first();
    }

}

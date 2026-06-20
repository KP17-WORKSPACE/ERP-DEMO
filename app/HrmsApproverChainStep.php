<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HrmsApproverChainStep extends Model
{
    //
    protected $table = 'hrms_approver_chain_steps';

    protected $fillable = [
        'approver_chain_id',
        'step_no',
        'role',
        'approver_id',
        'status',
        'comment',
        'acted_at',
        'l1_workload', 'l1_coverage', 'l1_eligibility', 'l1_duration_ok', 'l1_notice_compliance', 'l1_emergency', 'l1_recommended_action', 'l1_decision', 'l1_remark',
        'l2_balance', 'l2_unpaid', 'l2_encash', 'l2_cost', 'l2_policy', 'l2_docs', 'l2_decision', 'l2_remark', 'l2_balance_verify', 'l2_policy_verify', 'l2_docs_verify',
        'l3_limits', 'l3_critical', 'l3_blackout', 'l3_exceptional', 'l3_docs', 'l3_policy', 'l3_system', 'l3_payroll', 'l3_legal', 'l3_decision', 'l3_remark'
    ];

    public function approver()
    {
        return $this->belongsTo(\App\SmStaff::class, 'approver_id');
    }

    public function chain()
    {
    return $this->belongsTo(\App\HrmsApproverChain::class, 'approver_chain_id');
    }

    
 public function scopeActionableByHR($q, $userId)
    {
        return $q->where('status', 'P')
                 ->where('approver_id', (int) $userId)
                 ->where('role', 'HR');
    }

    

}

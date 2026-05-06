<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompensationRole extends Model
{
    protected $table = 'compensation_roles';
    protected $fillable = [
        'company_id',
        'doc_no',
        'doc_date',
        'employee_id',
        'transaction_type',
        'effective_date',
        'current_status',
        'created_by',
        'updated_by',
    ];

    // protected $dates = ['doc_date', 'effective_date', 'created_at', 'updated_at'];

    // Relationships
    public function employee()
    {
        return $this->belongsTo('App\SmStaff', 'employee_id');
    }

    public function company()
    {
        return $this->belongsTo('App\SysCompany', 'company_id');
    }

    public function promotionDetails()
    {
        return $this->hasOne('App\CompensationPromotionDetail', 'compensation_id');
    }

    public function demotionDetails()
    {
        return $this->hasOne('App\CompensationDemotionDetail', 'compensation_id');
    }

    public function salaryIncrementDetails()
    {
        return $this->hasOne('App\CompensationSalaryIncrementDetail', 'compensation_id');
    }

    public function approvals()
    {
        return $this->hasMany('App\CompensationApproval', 'compensation_id');
    }

    public function acknowledgement()
    {
        return $this->hasOne('App\CompensationAcknowledgement', 'compensation_id');
    }

    public function approvalHistory()
    {
        return $this->hasMany('App\CompensationApprovalHistory', 'compensation_id');
    }
}

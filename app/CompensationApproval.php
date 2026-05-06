<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompensationApproval extends Model
{
    protected $table = 'compensation_approvals';
    protected $fillable = [
        'compensation_id',
        'approval_level',
        'approver_id',
        'approver_role',
        'approval_status',
        'remarks',
        'approval_date',
    ];

    protected $dates = ['approval_date', 'created_at', 'updated_at'];

    // Relationships
    public function compensation()
    {
        return $this->belongsTo('App\CompensationRole', 'compensation_id');
    }

    public function approver()
    {
        return $this->belongsTo('App\SmStaff', 'approver_id');
    }
}

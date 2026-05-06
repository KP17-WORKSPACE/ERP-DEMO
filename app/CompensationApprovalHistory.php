<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompensationApprovalHistory extends Model
{
    protected $table = 'compensation_approval_history';
    protected $fillable = [
        'compensation_id',
        'approval_level',
        'action_by',
        'action_type',
        'action_remarks',
    ];

    protected $dates = ['action_date', 'created_at', 'updated_at'];
    public $timestamps = false; // Only action_date is used

    // Relationships
    public function compensation()
    {
        return $this->belongsTo('App\CompensationRole', 'compensation_id');
    }

    public function actionBy()
    {
        return $this->belongsTo('App\SmStaff', 'action_by');
    }
}

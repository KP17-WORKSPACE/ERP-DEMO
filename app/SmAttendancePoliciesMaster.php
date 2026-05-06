<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmAttendancePoliciesMaster extends Model
{
    //
    protected $table = 'sm_attendance_policies_master';

    protected $fillable = [
        'code',
        'attendance_policy',
        'min_working_hours',
        'half_day_after',
        'absent_below_hours',
        'late_mark_allowed',
        'late_mark_halfday',
        'auto_absent_after',
        'is_active',
    ];

    public $timestamps = true;
}

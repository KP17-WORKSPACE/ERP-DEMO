<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmAttendanceMaster extends Model
{
    //
    protected $table = 'sm_attendance_master';

    protected $fillable = [
        'code',
        'name',
        'attendance_policy',
        'shift_type',
        'start_time',
        'end_time',
        'work_hours_per_day',
        'grace_period',
        'min_working_hours',
        'half_day_after',
        'absent_below_hours',
        'late_mark_allowed',
        'late_mark_halfday',
        'auto_absent_after',
        'break_minutes',
        'description',
        'is_active',
    ];
}

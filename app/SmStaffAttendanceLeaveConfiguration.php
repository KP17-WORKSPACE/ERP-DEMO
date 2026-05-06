<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmStaffAttendanceLeaveConfiguration extends Model
{
    //

     protected $table = 'sm_staff_attendance_leave_configurations';

    protected $fillable = [
        'staff_id',
        'attendance_policy',
        'min_working_hours',
        'grace_period',
        'half_day_after',
        'absent_below_hours',
        'late_mark_allowed',
        'late_mark_halfday',
        'auto_absent_after',
        'leave_policy_type',
        'annual_leave',
        'sick_leave',
        'casual_leave',
        'comp_off_allowed',
        'carry_forward',
        'max_carry_forward',
        'leave_encashment',
        'shift_start_time',
        'shift_end_time',
        'weekly_off_days',
    ];

    protected $casts = [
    'weekly_off_days' => 'array',
];

    /**
     * Relationship with Staff (if exists)
     */
    public function staff()
    {
        return $this->belongsTo(SmStaff::class, 'staff_id');
    }

}

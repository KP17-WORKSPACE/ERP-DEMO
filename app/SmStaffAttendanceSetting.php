<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmStaffAttendanceSetting extends Model
{
    //

     protected $table = 'sm_staff_attendance_settings';

    protected $fillable = [
        'user_id',
        'staff_id',
        'shift_id',
        'policy_id',
        'weekly_off_days',
        'overrides',
    ];

    // Laravel 5.7 doesn’t auto-cast JSON, so handle manually in get/set
    public function getWeeklyOffDaysAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function setWeeklyOffDaysAttribute($value)
    {
        $this->attributes['weekly_off_days'] = json_encode($value);
    }

    public function shift()
    {
        return $this->belongsTo('App\SmShiftMaster', 'shift_id');
    }

    public function policy()
    {
        return $this->belongsTo('App\SmAttendancePoliciesMaster', 'policy_id');
    }
    
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmStaffAttendence extends Model
{

     protected $fillable = [
        'staff_id',
        'finger_print_id',
        'in_time',
        'out_time',
        'punch_in',
        'punch_out',
        'attendence_date',
        'attendence_type',
        'notes',
    ];


    public function StaffInfo(){
    	return $this->belongsTo('App\SmStaff', 'staff_id', 'user_id');
    }

    public function staff()
    {
    return $this->belongsTo(SmStaff::class, 'staff_id');
    }

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmShiftMaster extends Model
{
    //
     protected $table = 'sm_shifts_master';

    protected $fillable = [
        'name',
        'shift_type', // Fixed / Rotational / Custom
        'start_time',
        'end_time',
        'work_hours_per_day',
        'grace_period',
        'is_active',
    ];

    public $timestamps = true;
}

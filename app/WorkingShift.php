<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkingShift extends Model
{
   

    protected $table = 'working_shifts';

    protected $fillable = [
        'company_id',
        'shift_name',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}

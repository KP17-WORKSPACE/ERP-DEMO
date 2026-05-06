<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class SmLeaveType extends Model
{

    protected $table = 'sm_leave_types';
    protected $fillable = [
    'name', 'code', 'description', 'max_days_per_year', 'is_paid', 'is_active'
    ];

}


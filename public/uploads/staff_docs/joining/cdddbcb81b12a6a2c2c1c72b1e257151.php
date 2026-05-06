<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCompanyHrPayrollSetting extends Model
{
    //

     protected $table = 'sys_company_hrpayrollsetting';

    protected $fillable = [
        'company_id',
        'wps_establishment_id',
        'wps_bank',
        'wps_salary_file_code',
        'payroll_cycle',
        'payroll_start_day',
        'payroll_end_day',
        'weekly_off_day',
        'weekly_off_pattern',
        'gratuity_calculation_method',
        'attendance_policy',
        'minimum_working_hours',
        'grace_period_minutes',
        'half_day_after_hours',
        'absent_if_hours_below',
        'late_mark_count_allowed',
        'consecutive_late_to_halfday',
        'auto_mark_absent_after_days',
        'shift_start_time',
        'shift_end_time',
        'weekly_off_days',
        'leave_policy_type',
        'annual_leave_cl_sl',
        'sick_leave_sl',
        'casual_leave_cl',
        'comp_off_allowed',
        'carry_forward_unused_leaves',
        'max_carry_forward_days',
        'encashable_leaves'
    ];

    protected $casts = [
        'weekly_off_days' => 'array',
    ];
    
}

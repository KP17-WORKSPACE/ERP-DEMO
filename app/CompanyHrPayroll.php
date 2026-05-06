<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyHrPayroll extends Model
{
    //
    protected $table = 'company_hr_payrolls';

    protected $fillable = [
    'company_id',
    'wps_establishment_id',
    'wps_bank',
    'wps_salary_file_code',
    'payroll_cycle',          // monthly | bi-weekly | weekly
    'payroll_start',          // numeric (1–30)
    'payroll_end',            // numeric (1–30)
    'weekly_off',             // sunday..saturday
    'gratuity_method',        // basic_salary | gross_salary
    'insurance_provider',
    'insurance_policy_number',
    'insurance_policy_expiry', // DATE
];

    // Laravel 5.x: helpful cast for date
    protected $dates = ['insurance_policy_expiry', 'created_at', 'updated_at'];
    protected $casts = ['insurance_policy_expiry' => 'date:Y-m-d'];
    public function company()
    {
        // adjust class if your company model name differs
        return $this->belongsTo(SysCompany::class, 'company_id');
    }

    
}

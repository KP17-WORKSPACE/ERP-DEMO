<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCompanySetting extends Model
{
    //sys_company_settings


    protected $table = 'sys_company_settings';

    protected $fillable = [

        'company_id',

        // CODE SETTINGS
        'is_customer_code',
        'is_supplier_code',
        'is_account_code',
        'is_subaccount_code',

        // BASIC COMPANY SETTINGS
        'currency',
        'currency_symbol',
        'currency_digit',
        'currency_digit_display',
        'r_code',
        'p_code',
        'book_closed',
        'sales_code',
        'other_code',

        // HR PAYROLL SETTINGS
        'hr_wps_establishment_id',
        'hr_wps_bank',
        'hr_wps_salary_file_code',

        'hr_payroll_cycle',
        'hr_payroll_start',
        'hr_payroll_end',

        'hr_weekly_off',
        'hr_gratuity_method',

        'hr_insurance_provider',
        'hr_insurance_policy_number',
        'hr_insurance_policy_expiry'
    ];

    protected $casts = [
        'is_customer_code'     => 'boolean',
        'is_supplier_code'     => 'boolean',
        'is_account_code'      => 'boolean',
        'is_subaccount_code'   => 'boolean',

        'currency_digit'       => 'integer',
        'currency_digit_display' => 'integer',
        'book_closed'          => 'date',

        'hr_payroll_start'     => 'integer',
        'hr_payroll_end'       => 'integer',

        'hr_insurance_policy_expiry' => 'date',
    ];

    // RELATION
    public function company()
    {
        return $this->belongsTo(SysCompany::class, 'company_id');
    }

}

<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class EmployeeOnboardingBankDetail extends Model
{
    

    protected $table = 'employee_onboarding_bank_details';

    protected $fillable = [
        'staff_id',
        'bank_name',
        'bank_branch',
        'bank_ac_holder',
        'bank_ac_number',
        'iban_number',
        'swift_code',
        'bank_currency',
        'att_iban_letter',
    ];

    /**
     * Relationship: Bank Detail belongs to Employee
     */
    public function employee()
    {
        return $this->belongsTo(EmployeeOnboarding::class, 'staff_id');
    }

    public function currency()
    {
        return $this->belongsTo(SysCurrencySettings::class, 'bank_currency');
    }
}

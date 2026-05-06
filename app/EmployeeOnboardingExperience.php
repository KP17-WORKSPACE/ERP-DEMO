<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class EmployeeOnboardingExperience extends Model
{
   

    protected $table = 'employee_onboarding_experiences';

    protected $fillable = [
        'staff_id',
        'organization',
        'designation',
        'years',
        'months',
        'responsibilities',
        'certificate_path',
    ];

    /**
     * Relationship: Experience belongs to Employee
     */
    public function employee()
    {
        return $this->belongsTo(EmployeeOnboarding::class, 'staff_id');
    }
}

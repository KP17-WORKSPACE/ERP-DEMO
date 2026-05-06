<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class EmployeeOnboardingEducation extends Model
{


    protected $table = 'employee_onboarding_education';

    protected $fillable = [
        'staff_id',
        'qualification',
        'university',
        'specialization',
        'year',
        'result',
        'gpa',
        'mode',
        'country',
        'duration_years',
        'certificate_path',
    ];

    /**
     * Relationship: Education belongs to Employee
     */
    public function employee()
    {
        return $this->belongsTo(EmployeeOnboarding::class, 'staff_id');
    }
}

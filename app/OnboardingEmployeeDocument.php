<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OnboardingEmployeeDocument extends Model
{


    protected $table = 'onboarding_employee_documents';

    protected $fillable = [
        'staff_id',
        'group',
        'key',
        'name',
        'remarks',
        'path',
        'file_path',
        'expiry_date',
        'document_number'
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    /**
     * Relationship: Document belongs to Employee
     */
    public function employee()
    {
        return $this->belongsTo(EmployeeOnboarding::class, 'staff_id');
    }
}

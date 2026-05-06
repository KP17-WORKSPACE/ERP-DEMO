<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOnboarding extends Model
{

    protected $table = 'employees_onboarding';

    protected $fillable = [
        'document_number',

        'user_id',
        'role_id',
        'staff_no',

        'designation_id',
        'department_id',

        'company_id',
        'company_access',
        'main_company',

        'first_name',
        'middle_name',
        'last_name',
        'full_name',

        'fathers_first_name',
        'fathers_last_name',
        'mothers_first_name',
        'mothers_last_name',
        'father_mobile',
        'mother_mobile',
        'father_email',
        'mother_email',


        'date_of_birth',
        'date_of_joining',
        'date_of_resign',

        'gender_id',
        'place_of_birth',

        'email',
        'password',

        'mobile',
        'emergency_mobile',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_email',

        'emergency2_mobile',
        'emergency2_contact_name',
        'emergency2_contact_relationship',
        'emergency2_email',

        'ext_no',
        'marital_status',
        'merital_status',

        'religion',
        'nationality',
        'staff_rec_id',

        'staff_photo',

        'current_address',
        'permanent_address',

        'permanent_country',
        'permanent_state',
        'permanent_city',
        'permanent_area',
        'permanent_building_no',
        'permanent_flat_no',

        'current_country',
        'current_state',
        'current_city',
        'current_area',
        'current_building_no',
        'current_flat_no',

        'qualification',
        'experience',

        'epf_no',
        'basic_salary',
        'contract_type',
        'employment_type',
        'location',

        'casual_leave',
        'medical_leave',
        'metarnity_leave',

        'bank_account_name',
        'bank_account_no',
        'bank_name',
        'bank_brach',

        'paypal_account',
        'payoneer_account',
        'skrill_account',
        'stripe_account',
        'wepay_account',
        'amazon_account',

        'facebook_url',
        'twiteer_url',
        'linkedin_url',
        'instragram_url',

        'joining_letter',
        'resume',
        'other_document',

        'driving_license',
        'driving_license_ex_date',

        'notes',
        'reporting_manager',

        'revenue_target_weekly',
        'revenue_target_monthly',
        'revenue_target_quaterly',
        'revenue_target_yearly',

        'gp_target_weekly',
        'gp_target_monthly',
        'gp_target_quaterly',
        'gp_target_yearly',

        'target_month_from',
        'is_target',
        'brands',

        'auth_code',
        'auth_date',
        'auth_status',
        'apprioved_by',

        'type',
        'combind_user_id',

        'active_status',
        'delete_status',

        'created_by',
        'updated_by',
        'employee_salutation',
        'em1_salutation',
        'em2_salutation',
        'blood_group',

        'spouse_first_name',
        'spouse_last_name',
        'spouse_mobile',
        'spouse_email',
        'spouse_attachment',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_joining' => 'date',
        'date_of_resign' => 'date',
        'auth_date' => 'date',
        'driving_license_ex_date' => 'date',
        'target_month_from' => 'date',
    ];

    public function documents()
    {
        return $this->hasMany(OnboardingEmployeeDocument::class, 'staff_id');
    }

    public function bankDetails()
    {
        return $this->hasMany(EmployeeOnboardingBankDetail::class, 'staff_id');
    }

    public function educations()
    {
        return $this->hasMany(EmployeeOnboardingEducation::class, 'staff_id');
    }

    public function experiences()
    {
        return $this->hasMany(EmployeeOnboardingExperience::class, 'staff_id');
    }

    public function jobdetail()
    {
        return $this->hasOne(OnboardEmployeeJobDetail::class, 'staff_id');
    }


}

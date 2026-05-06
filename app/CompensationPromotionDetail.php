<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompensationPromotionDetail extends Model
{
    protected $table = 'compensation_promotion_details';
    protected $fillable = [
        'compensation_id',
        'promotion_type',
        'promotion_reason',
        'current_department_id',
        'new_department_id',
        'current_designation_id',
        'new_designation_id',
        'current_grade',
        'new_grade',
        'new_reporting_manager_id',
        'position_availability',
        'min_band_salary',
        'max_band_salary',
        'proposed_salary',
        'promotion_justification',
        'promotion_letter_path',
        'job_description_path',
        'training_plan_path',
    ];

    protected $dates = ['created_at', 'updated_at'];

    // Relationships
    public function compensation()
    {
        return $this->belongsTo('App\CompensationRole', 'compensation_id');
    }

    public function currentDepartment()
    {
        return $this->belongsTo('App\SmHumanDepartment', 'current_department_id');
    }

    public function newDepartment()
    {
        return $this->belongsTo('App\SmHumanDepartment', 'new_department_id');
    }

    public function currentDesignation()
    {
        return $this->belongsTo('App\SmDesignation', 'current_designation_id');
    }

    public function newDesignation()
    {
        return $this->belongsTo('App\SmDesignation', 'new_designation_id');
    }

    public function reportingManager()
    {
        return $this->belongsTo('App\SmStaff', 'new_reporting_manager_id');
    }
}

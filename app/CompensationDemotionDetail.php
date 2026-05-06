<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompensationDemotionDetail extends Model
{
    protected $table = 'compensation_demotion_details';
    protected $fillable = [
        'compensation_id',
        'demotion_type',
        'nature_of_demotion',
        'reason_for_demotion',
        'revised_department_id',
        'revised_designation_id',
        'revised_grade',
        'legal_compliance',
        'consent_status',
        'appeal_option',
        'warning_letters_path',
        'demotion_letter_path',
    ];

    protected $dates = ['created_at', 'updated_at'];

    // Relationships
    public function compensation()
    {
        return $this->belongsTo('App\CompensationRole', 'compensation_id');
    }

    public function revisedDepartment()
    {
        return $this->belongsTo('App\SmHumanDepartment', 'revised_department_id');
    }

    public function revisedDesignation()
    {
        return $this->belongsTo('App\SmDesignation', 'revised_designation_id');
    }
}

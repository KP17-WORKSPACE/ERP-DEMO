<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmStaffEducationQualification extends Model
{
    //
    protected $fillable = [
        'staff_id',
        'qualification','university','specialization',
        'year','result','gpa','mode','country','duration_years',
        'certificate_path',
    ];
}

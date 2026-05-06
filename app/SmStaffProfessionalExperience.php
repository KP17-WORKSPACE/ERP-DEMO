<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmStaffProfessionalExperience extends Model
{
    //
     protected $fillable = [
        'staff_id',
        'organization',
        'designation',
        'years',
        'months',
        'responsibilities',
        'certificate_path',
    ];
}

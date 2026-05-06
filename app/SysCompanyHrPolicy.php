<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCompanyHrPolicy extends Model
{
    //
     protected $table = 'sys_company_hr_policies';

    protected $fillable = [
        'company_id',
        'policy_date',
        'policy_name',
        'policy_category',
        'policy_valid',
        'view_to_employees',
        'policy_file',
        'policy_details',
    ];

    protected $casts = [
        'policy_date'        => 'date',
        'policy_valid'       => 'date',
        'view_to_employees'  => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(SysCompany::class, 'company_id');
    }
}

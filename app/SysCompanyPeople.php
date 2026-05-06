<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCompanyPeople extends Model
{
    //

    protected $table = 'sys_company_people';

    protected $fillable = [
        'company_id',
        'type',              // enum: owner, sponsor, contact
        'salutation',        // Mr, Mrs, Miss, Ms, Dr
        'first_name',
        'last_name', 
        'name',
        'mobile',
        'email',
        'designation',       // Only for contact person
        'share_percentage',  // Only for owners
        'passport_copy',
        'emirates_id',
        'visa_copy',
    ];

    // Company Relation
    public function company()
    {
        return $this->belongsTo(SysCompany::class, 'company_id', 'id');
    }

    // Documents Relation
    public function documents()
    {
        return $this->hasMany(SysCompanyPeopleDocument::class, 'people_id', 'id');
    }
    
}

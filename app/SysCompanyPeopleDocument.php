<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCompanyPeopleDocument extends Model
{
    protected $table = 'sys_company_people_documents';

    protected $fillable = [
        'people_id',
        'document_name',
        'document_no',
        'issue_date',
        'expiry_date',
        'attachment',
    ];

    // People Relation
    public function person()
    {
        return $this->belongsTo(SysCompanyPeople::class, 'people_id', 'id');
    }
}
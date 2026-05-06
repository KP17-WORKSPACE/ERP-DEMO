<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCompanyDocumentItem extends Model
{
    protected $table = 'sys_company_document_items';

    protected $fillable = [
        'company_id',
        'document_name',
        'document_number',
        'document_date',
        'expiry_date',
        'attachment_file',
        'document_type'
    ];

    protected $dates = [
        'document_date',
        'expiry_date',
        'created_at',
        'updated_at',
    ];

    public function company()
    {
        return $this->belongsTo(SysCompany::class, 'company_id');
    }
}

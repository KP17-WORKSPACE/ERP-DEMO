<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCompanyCompliance extends Model
{
    protected $table = 'sys_company_compliances';

    protected $fillable = [
        'company_id',
        'trade_license_no',
        'license_issue_date', 
        'license_expiry_date',
        'issuing_authority',
        'tax_applicable',
        'vat_registration_number',
        'vat_percentage',
        'vat_date',
        'vat_certificate',
        'vat_issuing_authority',
        'corporate_tax_number',
        'corporate_tax_date',
        'corporate_tax_vat',
        'corporate_tax_certificate',
        'corporate_issuing_authority',
        'business_license_upload',
        'attachment'
    ];

    // Relationship: each compliance belongs to one company
    public function company()
    {
        return $this->belongsTo(SysCompany::class, 'company_id');
    }
}

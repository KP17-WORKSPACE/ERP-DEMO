<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCompanyDocument extends Model
{
    //
    protected $table = 'sys_company_documents';

    protected $fillable = [
        'company_id',

        // Establishment
        'establishment_file',
        'establishment_expiry',
        'establishment_number',
        'establishment_start_date',

        // Immigration
        'immigration_file',
        'immigration_expiry',
        'immigration_number',
        'immigration_start_date',

        // Labour Card
        'labour_file',
        'labour_expiry',
        'labour_number',
        'labour_start_date',

        // Chamber of Commerce
        'chamber_file',
        'chamber_expiry',
        'chamber_number',
        'chamber_start_date',

        // Insurance Certificate
        'insurance_file',
        'insurance_certificate_expiry',
        'insurance_certificate_number',
        'insurance_start_date',

        // MOA/AOA
        'moa_aoa_number',
        'moa_aoa_expiry',
        'moa_aoa_file',

        // Board Resolution
        'board_resolution_number',
        'board_resolution_expiry',
        'board_resolution_file',

        // Power of Attorney
        'poa_number',
        'poa_expiry',
        'poa_file',
    ];

    // Relationship
    public function company()
    {
        return $this->belongsTo(SysCompany::class, 'company_id');
    }
}

<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class CompanyPdfSetting extends Model
{
    

    protected $table = 'company_pdf_settings';

    protected $fillable = [
        'company_id',
        'attachment',
        'is_primary',
        'is_active',
        'type', // header/footer/watermark/firstpage
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship: belongs to Company
     */
    public function company()
    {
        return $this->belongsTo(SysCompany::class);
    }

    
}

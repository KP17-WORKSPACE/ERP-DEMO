<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCompany extends Model
{
    protected $table = 'sys_company';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'company_name','company_address','country','city','email','website','telephone','fax','mobile','vat_number','trade_license_no','trade_license_exp_date','bank_name','account_number','iban_no','finance_code','branch_swift_code','company_logo','digital_stamp','status','created_by','updated_by','created_at','updated_at','pdf_header','pdf_footer','pdf_watermark','pdf_first_page','sales_code','other_code','currency_id','sort_id','company_vat_rate','decimal_point','trade_name','legal_entity_type','industry','parent_company',
        'date_of_incorporation','country','state','city','company_address',
        'sales_code','other_code','currency','currency_digit','book_closed',
        'company_logo_path','digital_stamp_path','company_profile_path','owner_name','owner_mobile','owner_email',
        'owner_passport_path','owner_emirates_id_path','owner_visa_path',
        'sponsor_name','sponsor_mobile','sponsor_email',
        'sponsor_passport_path','sponsor_emirates_id_path','sponsor_visa_path',
        'contact_person_name','contact_person_mobile','contact_person_email','contact_person_designation',
        'contact_passport_path','contact_emirates_id_path','contact_visa_path','owner_passport_copy','owner_emirates_id','owner_visa_copy',
        'contact_emirates_id','contact_passport_copy','contact_visa_copy',
        'sponsor_passport_copy','sponsor_emirates_id','sponsor_visa_copy','company_profile','company_code','business_entity_id','industry_id','business_sector_id','parent_company_id',
        'linkedin',
        'facebook',
        'instagram',
        'twitter_x',
        'youtube',
        'other_social',
        'license_issue_date',
        'company_type'
    ];
     protected $casts = [
        'date_of_incorporation' => 'date',
        'book_closed'           => 'date',
        'contact_sections' => 'array'
    ];

    public function country(){
	    return $this->belongsTo('App\SysCountries', 'country', 'id');
	}
    public function countryname(){
	    return $this->belongsTo('App\SysCountries', 'country', 'id');
	}
    public function currency(){
	    return $this->belongsTo('App\SysCurrency', 'currency_id', 'id');
	}

     public function hrPolicies()
    {
        // FK = company_id on sys_company_hr_policies
        return $this->hasMany(SysCompanyHrPolicy::class, 'company_id')
                    ->orderBy('policy_date', 'desc');
    }

    public function compliance()
    {
    return $this->hasOne(SysCompanyCompliance::class, 'company_id');
    }

    public function leaveRequests()
    {
    return $this->hasMany(SmLeaveRequest::class, 'company_id', 'id');
    }

    public function people()
    {
    return $this->hasMany(SysCompanyPeople::class, 'company_id', 'id');
    }

    public function banking()
    {
    return $this->hasMany(SysCompanyBanking::class, 'company_id', 'id');
    }
    public function documents()
    {
    return $this->hasMany(SysCompanyDocument::class, 'company_id', 'id');
    }

    public function countryRelation()
    {
    return $this->belongsTo(SysCountries::class, 'country', 'id');
    }

    public function stateRelation()
    {
    return $this->belongsTo(SysStates::class, 'state', 'id');
    }

    public function businessEntity()
    {
    return $this->belongsTo(SmBusinessActivity::class, 'business_entity_id', 'id');
    }

    public function businessIndustry()
    {
    return $this->belongsTo(SmIndustry::class, 'industry_id', 'id');
    }

     public function businessSector()
    {
    return $this->belongsTo(SmBusinessActivity::class, 'business_sector_id', 'id');
    }

    public function parentCompany()
    {
    return $this->belongsTo(SysCompany::class, 'parent_company_id', 'id');
    }

    public function settings()
    {
    return $this->hasOne(SysCompanySetting::class, 'company_id');
    }


    



}

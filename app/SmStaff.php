<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
// use App\SmStaff; 
use App\SmSupplier;
use App\SmItem;
use Illuminate\Support\Facades\Storage;

class SmStaff extends Model
{

	protected $table = 'sm_staffs';
	protected $primaryKey = 'id';
	protected $appends = ['staff_photo_public_url'];


	protected $fillable = [
		'id',
		'user_id',
		'role_id',
		'finger_print_id',
		'staff_no',
		'designation_id',
		'department_id',
		'company_id',
		'company_access',
		'main_company',
		'company_name',
		'first_name',
		'first_name_full',
		'last_name',
		'full_name',
		
		'date_of_birth',
		'date_of_joining',
		'date_of_resign',
		'gender_id',
		'email',
		'mobile',
		'emergency_mobile',
		'ext_no',
		'marital_status',
		'merital_status',
		'staff_photo',
		'current_address',
		'permanent_address',
		// New Permanent Address Fields
		'permanent_country',
		'permanent_state',
		'permanent_city',
		'permanent_area',
		'permanent_building_no',
		'permanent_flat_no',
		// New Current Address Fields
		'current_country',
		'current_state',
		'current_city',
		'current_area',
		'current_building_no',
		'current_flat_no',
		'qualification',
		'experience',
		'epf_no',
		'basic_salary',
		'contract_type',
		'location',
		'casual_leave',
		'medical_leave',
		'metarnity_leave',
		'bank_account_name',
		'bank_account_no',
		'bank_name',
		'bank_brach',
		'paypal_account',
		'payoneer_account',
		'skrill_account',
		'stripe_account',
		'wepay_account',
		'amazon_account',
		'facebook_url',
		'twiteer_url',
		'linkedin_url',
		'instragram_url',
		'joining_letter',
		'resume',
		'other_document',
		'notes',
		'active_status',
		'delete_status',
		'driving_license',
		'driving_license_ex_date',
		'created_by',
		'updated_by',
		'created_at',
		'updated_at',
		'auth_code',
		'auth_date',
		'auth_status',
		'type',
		'revenue_target_weekly',
		'revenue_target_monthly',
		'revenue_target_quaterly',
		'revenue_target_yearly',
		'gp_target_weekly',
		'gp_target_monthly',
		'gp_target_quaterly',
		'gp_target_yearly',
		'target_month_from',
		'combind_user_id',
		'is_target',
		'brands',
		'reporting_manager',
		'employment_type',


		 'employee_salutation',
        'em1_salutation',
        'em2_salutation',
        'blood_group',

        'spouse_first_name',
        'spouse_last_name',
        'spouse_mobile',
        'spouse_email',
        'spouse_attachment',

		 'fathers_first_name',
        'fathers_last_name',
        'mothers_first_name',
        'mothers_last_name',
        'father_mobile',
        'mother_mobile',
        'father_email',
        'mother_email',
		'place_of_birth',
		'emergency_contact_name',
		'emergency_contact_relationship',
		'emergency_mobile',
		'emergency_email',
		'emergency2_contact_name',
		'emergency2_contact_relationship',
		'emergency2_mobile',
		'emergency2_email',
		'grade'

	];

	public function roles()
	{
		return $this->belongsTo('App\Role', 'role_id', 'id');
	}

	public function departments()
	{
		return $this->belongsTo('App\SmHumanDepartment', 'department_id', 'id');
	}

	public function designations()
	{
		return $this->belongsTo('App\SmDesignation', 'designation_id', 'id');
	}

	public function company()
	{
		return $this->belongsTo('App\SysCompany', 'company_id', 'id');
	}
	public function maincompany()
	{
		return $this->belongsTo('App\SysCompany', 'main_company', 'id');
	}

	public function genders()
	{
		return $this->belongsTo('App\SmBaseSetup', 'gender_id', 'id');
	}

	public static function getNumberVendor()
	{
		return SmSupplier::all()->count();
	}
	public static function getNumberCustomer()
	{
		return SysCustomer::get()->count();
	}

	public static function getNumberStaff()
	{
		return SmStaff::where([['role_id', '!=', 2], ['role_id', '!=', 7]])->get()->count();
	}


	public static function getNumberItemStock()
	{

		$product_receive = SmItemReceive::sum('qnt');
		$product_sale = SmTenderProduct::sum('qnt');
		return $product_receive - $product_sale;
	}


	public function getStaffPhotoPublicUrlAttribute(): string
	{
		if ($this->staff_photo) {
			return asset('public/' . $this->staff_photo);
		}

		return asset('public/uploads/staff/demo/staff.png');
	}

	public function jobDetail()
	{
		return $this->hasOne(SmStaffJobDetail::class, 'staff_id');
	}

	// Bank detail (1-to-1) - kept for backward compatibility
	public function bankDetail()
	{
		return $this->hasOne(SmStaffBankDetail::class, 'staff_id');
	}

	// Bank details (1-to-many) - for multiple bank accounts
	public function bankDetails()
	{
		return $this->hasMany(SmStaffBankDetail::class, 'staff_id');
	}

	public function educations()
	{
		return $this->hasMany(SmStaffEducationQualification::class, 'staff_id');
	}

	public function experiences()
	{
		return $this->hasMany(SmStaffProfessionalExperience::class, 'staff_id');
	}

	// Education (1-to-many)
	public function educationQualifications()
	{
		return $this->hasMany(SmStaffEducationQualification::class, 'staff_id');
	}

	// Professional experience (1-to-many)
	public function professionalExperiences()
	{
		return $this->hasMany(SmStaffProfessionalExperience::class, 'staff_id');
	}

	// Documents (1-to-many)
	public function documents()
	{
		return $this->hasMany(SmStaffDocument::class, 'staff_id');
	}

	public function getCompanyAccessIdsAttribute()
	{
		$val = $this->attributes['company_access'] ?? null;

		if (is_array($val))
			$arr = $val;
		elseif (is_string($val)) {
			$trim = trim($val);
			$json = json_decode($trim, true);
			if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
				$arr = $json;
			} else {
				preg_match_all('/\d+/', $trim, $m);
				$arr = $m[0] ?? [];
			}
		} else
			$arr = [];

		// sanitize & preserve order
		$ids = [];
		foreach ($arr as $v) {
			$id = (int) $v;
			if ($id && !in_array($id, $ids, true))
				$ids[] = $id;
		}
		return $ids;
	}

	public function companyAccessCompanies()
	{
		$ids = $this->company_access_ids;
		if (empty($ids))
			return collect();

		$order = implode(',', $ids); // MySQL only
		return \App\SysCompany::whereIn('id', $ids)
			->orderByRaw("FIELD(id, $order)")
			->get();
	}

	public function getGenderNameAttribute()
	{
		return optional($this->gender)->base_setup_name
			?: optional($this->gender)->name
			?: '—';
	}

	public function nationalityCountry()
	{
		return $this->belongsTo(\App\SmCountry::class, 'nationality', 'id');
	}

	public function getNationalityNameAttribute()
	{
		return optional($this->nationalityCountry)->name
			?: optional($this->nationalityCountry)->country_name
			?: '—';
	}

	public function user()
	{
	return $this->belongsTo(\App\User::class, 'user_id', 'id');
	}


	public function staff()
	{
	return $this->belongsTo(\App\SmStaff::class, 'staff_id');
	}

	public function reportingManagernew()
	{
		return $this->belongsTo(\App\SmStaff::class, 'reporting_manager', 'id')
		->withDefault(); // avoids null errors
	}
	
	public function reportingManager()
	{
		return $this->belongsTo(User::class, 'reporting_manager_id', 'id')
		->withDefault(); // avoids null errors
	}

    public function teamMembers()
    {
        return $this->hasMany(SmStaff::class, 'reporting_manager');
    }

    public function attendances()
    {
        return $this->hasMany(SmStaffAttendence::class, 'staff_id');
    }

	public function activities()
	{
		return $this->hasMany(StaffActivity::class, 'user_id');
	}

	


}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmStaffJobDetail extends Model
{
    //
     protected $fillable = [
        'staff_id','role_id','designation_id','department_id','employment_type','reporting_manager','probation_end_date','shift_id','target_type',
        'work_location','work_hours','ext_no','salary_basic','salary_allowances','salary_gross',
        'visa_company_name','working_company_name','company_access','is_target',
        'revenue_target_weekly','revenue_target_monthly','revenue_target_quaterly','revenue_target_yearly','grade',
        'gp_target_weekly','gp_target_monthly','gp_target_quaterly','gp_target_yearly',
        'target_month_from','brand_ids','date_of_joining','att_resume','att_offer_letter','att_signed_contract','week_off','company_email','company_mobile','salary_other_allowances','transport_allowance','other_benefits',
        'target_period','revenue_target','gp_target','channel_distribution'
    ];

   protected $casts = [
        'company_access'   => 'array',
        'combind_user_ids' => 'array',
        'brand_ids'        => 'array',
        'reporting_manager' => 'array',
    ];

    public function reportingManagerModels()
    {
        // reporting_manager may be stored as JSON array, CSV string, or array
        $raw = $this->reporting_manager;

        if (is_string($raw)) {
            $parts = array_map('trim', explode(',', $raw));
        } elseif (is_array($raw)) {
            $parts = $raw;
        } else {
            // try JSON decode fallback
            $parts = [];
            if ($raw) {
                $j = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($j)) {
                    $parts = $j;
                }
            }
        }

        // sanitize and keep only positive ints, preserving order
        $ids = array_values(array_filter(array_map(function ($v) {
            $v = trim((string) ($v ?? ''));
            if ($v === '') return null;
            return (int) $v;
        }, $parts), function ($v) {
            return $v > 0;
        }));

        if (empty($ids)) {
            return collect();
        }

        $order = implode(',', $ids);

        return SmStaff::whereIn('id', $ids)
            ->orderByRaw("FIELD(id, $order)")
            ->get();
    }

public function departments()
	{
		return $this->belongsTo('App\SmHumanDepartment', 'department_id', 'id');
	}

	public function designations()
	{
		return $this->belongsTo('App\SmDesignation', 'designation_id', 'id');
	}


     public function getReportingManagerNamesAttribute()
    {
        return $this->reportingManagerModels()->map(function ($s) {
            return $s->full_name; // uses SmStaff@getFullNameAttribute
        })->values()->all();
    }

    public function company()
	{
		return $this->belongsTo('App\SysCompany', 'working_company_name', 'id');
	}

    public function visacompany(){
        return $this->belongsTo('App\SysCompany', 'visa_company_name', 'id');
    }

    
    public function companyAccessCompanies()
	{
		$raw = $this->company_access;
		if (empty($raw)) return collect();

		// Normalize to array: accept array, JSON string, CSV/string with digits
		if (is_array($raw)) {
			$arr = $raw;
		} elseif (is_string($raw)) {
			$trim = trim($raw);
			$json = json_decode($trim, true);
			if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
				$arr = $json;
			} else {
				preg_match_all('/\d+/', $trim, $m);
				$arr = $m[0] ?? [];
			}
		} else {
			$arr = [];
		}

		// sanitize & preserve order
		$ids = [];
		foreach ($arr as $v) {
			$id = (int) $v;
			if ($id && !in_array($id, $ids, true)) $ids[] = $id;
		}

		if (empty($ids)) return collect();

		$order = implode(',', $ids); // MySQL only
		return \App\SysCompany::whereIn('id', $ids)
			->orderByRaw("FIELD(id, $order)")
			->get();
	}

    public function shift()
    {   
        return $this->belongsTo('App\WorkingShift', 'shift_id', 'id');
    }

 public function weeklyOffModels()
{
    $values = array_filter(array_map('trim', explode(',', $this->week_off)));
 

    return \App\WeeklyOff::whereIn('id', $values)->get();
}



}
